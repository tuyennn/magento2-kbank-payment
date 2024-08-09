define([
    'jquery',
    'ko',
    'uiComponent',
    'underscore',
    'GhoSter_KbankPayments/js/model/installment'
], function ($, ko, Component, _, installment) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'GhoSter_KbankPayments/payment/installment-renderer/default'
        },

        /**
         * @returns {*}
         */
        initObservable: function () {
            this._super();
            this.isSelected = ko.computed(function () {
                var isSelected = false,
                    installmentInformation = installment.installmentInformation();

                if (installmentInformation) {
                    isSelected = installmentInformation.smartpay_id == this.installment().smartpay_id &&
                        installmentInformation.payment_term == this.installment().payment_term; //eslint-disable-line eqeqeq
                }

                return isSelected;
            }, this);

            return this;
        },

        /**
         * @inheritdoc
         */
        isChecked: ko.computed(function () {
            return installment.installmentInformation() ? installment.installmentInformation().smartpay_id : null;
        }),

        /**
         * @inheritdoc
         */
        selectInstallment: function () {
            installment.installmentInformation(this.installment());
        },

        /**
         * @returns {string}
         */
        getInstallmentElmId: function () {
            return this.paymentMethodCode() + '-smartpayid-' + this.installment().smartpay_id;
        },

        /**
         * @returns {string}
         */
        getInstallmentElmName: function () {
            return 'payment[' + this.paymentMethodCode() + '][smartpayid-' + this.installment().smartpay_id + ']';
        }
    });
});
