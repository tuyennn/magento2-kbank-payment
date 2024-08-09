define(
    [
        'Magento_Checkout/js/view/payment/default',
        'ko',
        'jquery',
        'underscore',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/quote',
        'GhoSter_KbankPayments/js/action/validate-order-status',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function (
        Component,
        ko,
        $,
        _,
        placeOrderAction,
        fullScreenLoader,
        quote,
        validateOrderStatusAction
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'GhoSter_KbankPayments/payment/kbank-embedded-fullpayment',
                paymentReady: false,
                inActionBodyClass: 'kbank-embedded-in-action',
                grandTotalAmount: null,
                embeddedFormPopup: null,
                orderId: null,
                isUseInstallment: false,
                rendererTemplates: [],
                embeddedFormElmPrefix: 'kbank-embedded-',
                embeddedFormElmPostfix: '-hidden'
            },
            redirectAfterPlaceOrder: false,
            isInitializedTrigger: ko.observable(false),
            isPopupClicked: ko.observable(false),
            isInAction: ko.observable(false),

            initialize: function () {
                this._super();
                var context = this;

                $(document)
                    .on('ghoster:closeKbankPopup', function () {
                        context.isInAction(false);
                    });

                this.isUseInstallment = false;
            },

            /** @inheritdoc */
            initConfig: function () {
                this._super();
                // the list of child components that are responsible for address rendering
                return this;
            },

            /**
             * Initialize form elements
             */
            initKbankFormElement: function () {
                var script,
                    hiddenEmbeddedFormElmId = this.getHiddenButtonEmbeddedFormElmId();

                script = document.querySelector('script[src^="' + this.getKbankSrc() + '"]');

                if (!script) {
                    script = document.createElement('script');
                    script.src = this.getKbankSrc();
                    this.setDataForScript(script);
                    document.getElementById(hiddenEmbeddedFormElmId).appendChild(script);
                } else {
                    this.setDataForScript(script);
                }
            },

            /**
             * Set data for script
             *
             * @param script
             */
            setDataForScript: function (script) {
                script.setAttribute('data-apikey', window.checkoutConfig.payment[this.getCode()].publicKey);
                script.setAttribute('data-amount', quote.totals()['base_grand_total']);
                script.setAttribute('data-currency', window.checkoutConfig.quoteData.quote_currency_code);
                script.setAttribute('data-payment-methods', 'card');
                script.setAttribute('data-name', window.checkoutConfig.payment[this.getCode()]['shopName']);
                script.setAttribute('data-mid', window.checkoutConfig.payment[this.getCode()]['merchantId']);
            },

            /**
             * Get action url for payment method popup.
             * @returns {String}
             */
            getTokenProcessActionUrl: function () {
                return window.checkoutConfig.payment[this.item.method]['processTokenActionUrl'];
            },

            /**
             * @returns {String}
             */
            getEmbeddedFormElmId: function () {
                return this.embeddedFormElmPrefix + this.getCode();
            },

            /**
             * @returns {String}
             */
            getHiddenButtonEmbeddedFormElmId: function () {
                return this.embeddedFormElmPrefix + this.getCode() + this.embeddedFormElmPostfix;
            },

            /**
             * @returns {String}
             */
            getKbankSrc: function () {
                return window.checkoutConfig.payment[this.getCode()].jsSrc;
            },

            /**
             * @returns {String}
             */
            getPublicKey: function () {
                return window.checkoutConfig.payment[this.getCode()].publicKey;
            },

            /**
             * @return {exports}
             */
            initObservable: function () {
                this._super()
                    .observe(['paymentReady']);

                this.grandTotalAmount = quote.totals()['base_grand_total'];

                quote.totals.subscribe(function () {
                    if (this.grandTotalAmount !== quote.totals()['base_grand_total']) {
                        this.grandTotalAmount = quote.totals()['base_grand_total'];
                    }
                }, this);

                this.isInAction.subscribe(function (isInAction) {
                    this.processKbankPopup(isInAction);
                }, this);

                return this;
            },

            /**
             * Open popup for the payment
             */
            processKbankPopup: function (isInAction) {
                var self = this;

                if (!window.hasOwnProperty('KPayment')) {
                    return this;
                }

                if (!this.isInitializedTrigger()) {
                    window.KPayment.onClose(function () {
                        $(document).trigger('ghoster:closeKbankPopup');
                    });

                    this.isInitializedTrigger(true);
                }

                if (isInAction) {
                    self.initKbankFormElement();
                    window.KPayment.create();
                    window.KPayment.setMid(window.checkoutConfig.payment[this.getCode()]['merchantId']);
                    window.KPayment.setPublickey(window.checkoutConfig.payment[this.getCode()]['publicKey']);
                    window.KPayment.setName(window.checkoutConfig.payment[this.getCode()]['shopName']);
                    window.KPayment.setAmount(quote.totals()['base_grand_total']);
                    window.KPayment.setCurrency(window.checkoutConfig.quoteData.quote_currency_code);
                    window.KPayment.setPaymentMethods('card');
                    window.KPayment.show();

                    this.isPopupClicked(true);
                }

                if (!isInAction
                    && this.isPopupClicked()
                    && !!this.orderId
                ) {
                    return $.when(
                        validateOrderStatusAction(self.orderId)
                    ).done(function (response) {
                        if (!response) {
                            $.mage.redirect(
                                window.checkoutConfig.payment[self.getCode()]['failureUrl']
                            );
                        }
                    }).fail(function () {
                        $.mage.redirect(
                            window.checkoutConfig.payment[self.getCode()]['failureUrl']
                        );
                    });
                }
            },

            /**
             * @return {*}
             */
            isPaymentReady: function () {
                return this.paymentReady();
            },

            /**
             * Places order in pending payment status.
             */
            placePendingPaymentOrder: function () {
                if (this.placeOrder()) {
                    fullScreenLoader.startLoader();
                    this.isInAction(true);
                }
            },

            /**
             * Show popup after order placed
             * @returns {*|boolean}
             */
            getPlaceOrderDeferredObject: function () {
                var self = this;

                return $.when(
                    placeOrderAction(this.getData(), this.messageContainer)
                ).done(
                    function (orderId) {
                        if (!isNaN(orderId)) {
                            self.orderId = orderId;
                            self.isInAction(true);
                        }
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function () {
                        fullScreenLoader.stopLoader();
                        self.isInAction(false);
                    });
            },

            /**
             * Hide loader when popup is fully loaded.
             */
            popupLoaded: function () {
                fullScreenLoader.stopLoader();
            },


            /**
             * @return {Boolean}
             */
            selectPaymentMethod: function () {
                this.initKbankFormElement();
                return this._super();
            },

            /**
             * @return {Boolean}
             */
            validate: function () {
                return true;
            }
        });
    }
);
