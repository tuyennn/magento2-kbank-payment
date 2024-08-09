define(
    [
        'Magento_Checkout/js/view/payment/default',
        'ko',
        'jquery',
        'underscore',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/template',
        'text!GhoSter_KbankPayments/template/payment/form/embedded.html',
        'Magento_Checkout/js/model/quote',
        'GhoSter_KbankPayments/js/model/alert',
        'GhoSter_KbankPayments/js/model/installment',
        'GhoSter_KbankPayments/js/action/validate-order-status',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mageUtils',
        'uiLayout',
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
        mageTemplate,
        embeddedFormTemplate,
        quote,
        alert,
        installment,
        validateOrderStatusAction,
        additionalValidators,
        utils,
        layout
    ) {
        'use strict';

        var defaultRendererTemplate = {
            parent: '${ $.$data.parentName }',
            name: '${ $.$data.name }',
            component: 'GhoSter_KbankPayments/js/view/payment/kbank-embedded/installment-renderer/default',
            provider: 'checkoutProvider'
        };

        return Component.extend({
            defaults: {
                template: 'GhoSter_KbankPayments/payment/kbank-embedded-installment',
                paymentReady: false,
                inActionBodyClass: 'kbank-embedded-in-action',
                grandTotalAmount: null,
                embeddedFormPopup: null,
                orderId: null,
                isUseInstallment: false,
                isPreDefinedInstallment: false,
                selectedBankInstallment: installment.getInstallmentInformation()(),
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

                this.isUseInstallment = window.checkoutConfig.payment[this.item.method]['isUseInstallment'] || false;
                this.isPreDefinedInstallment = window.checkoutConfig.payment[this.item.method]['isPreDefinedInstallment'] || false;
                this.initChildren();

            },

            /** @inheritdoc */
            initConfig: function () {
                this._super();
                // the list of child components that are responsible for address rendering
                this.rendererComponents = {};

                return this;
            },

            /** @inheritdoc */
            initChildren: function () {
                if (this.isUseInstallment && this.isPreDefinedInstallment) {
                    _.each(window.checkoutConfig.payment[this.item.method]['allowedInstallment'], this.createRendererComponent, this);
                }
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
             * Create new component that will render given installment in the address list
             *
             * @param {Object} installment
             * @param {*} index
             */
            createRendererComponent: function (installment, index) {
                var rendererTemplate, templateData, rendererComponent, paymentMethodCode;

                paymentMethodCode = this.item.method;

                if (index in this.rendererComponents) {
                    this.rendererComponents[index].installment(installment);
                } else {
                    // rendererTemplates are provided via layout
                    rendererTemplate = defaultRendererTemplate;

                    templateData = {
                        parentName: this.name,
                        name: index
                    };
                    rendererComponent = utils.template(rendererTemplate, templateData);
                    utils.extend(rendererComponent, {
                        installment: ko.observable(installment),
                        paymentMethodCode: ko.observable(paymentMethodCode)

                    });
                    layout([rendererComponent]);
                    this.rendererComponents[index] = rendererComponent;
                }
            },

            /**
             * @return {exports}
             */
            initObservable: function () {
                this._super()
                    .observe(['paymentReady', 'selectedBankInstallment']);

                this.grandTotalAmount = quote.totals()['base_grand_total'];

                quote.totals.subscribe(function () {
                    if (this.grandTotalAmount !== quote.totals()['base_grand_total']) {
                        this.grandTotalAmount = quote.totals()['base_grand_total'];
                    }
                }, this);

                installment.installmentInformation.subscribe(function (installmentData) {
                    this.selectedBankInstallment(installmentData);
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

                    var selectedBankInstallment = this.selectedBankInstallment();

                    if (this.isUseInstallment && this.isPreDefinedInstallment && !_.isEmpty(selectedBankInstallment)) {
                        window.KPayment.setSmartpayId(selectedBankInstallment['smartpay_id']);
                        window.KPayment.setTerm(selectedBankInstallment['payment_term']);
                    }

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
             * @returns {Object}
             */
            getData: function () {
                var self = this,
                    selectedBankInstallment = this.selectedBankInstallment();

                return {
                    method: this.getCode(),
                    'additional_data': {
                        smartpay_id: self.isUseInstallment && self.isPreDefinedInstallment
                            ? selectedBankInstallment['smartpay_id']
                            : '',
                        term: self.isUseInstallment && self.isPreDefinedInstallment
                            ? selectedBankInstallment['payment_term']
                            : '',
                        terminal_id: self.isUseInstallment && self.isPreDefinedInstallment
                            ? window.checkoutConfig.payment[self.getCode()]['terminalId']
                            : ''
                    }
                };
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
                if (!this.validate()) {
                    alert({content: $.mage.__('Please specify installment option')});
                    return false;
                }

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
             *
             * @returns {*}
             */
            getAllowedInstallments: function () {
                return this.rendererComponents;
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
                if (!this.isUseInstallment || !this.isPreDefinedInstallment) {
                    return true;
                }

                return !_.isEmpty(this.selectedBankInstallment()['smartpay_id']);
            }
        });
    }
);
