/**
 * @api
 */
define([
    'ko',
    'underscore',
    'domReady!'
], function (ko, _) {
    'use strict';

    var proceedInstallmentData = function (data) {
        let installmentData = {};
        if (_.isObject(data)) {
            installmentData['payment_term'] = data.hasOwnProperty('payment_term') ? data.payment_term : null;
            installmentData['smartpay_id'] = data.hasOwnProperty('smartpay_id') ? data.smartpay_id : null;
            installmentData['installment_title'] = data.hasOwnProperty('installment_title') ? data.installment_title : null;
        }
        return installmentData;
        },
        installmentData = proceedInstallmentData(window.checkoutConfig.quoteData),
        installmentInformation = ko.observable(installmentData),
        quoteData = window.checkoutConfig.quoteData;

    return {
        installmentInformation: installmentInformation,

        /**
         * @return {*}
         */
        getQuoteId: function () {
            return quoteData['entity_id'];
        },

        /**
         *
         * @return {*}
         */
        getInstallmentInformation: function () {
            return installmentInformation;
        },

        /**
         * @param {Object} data
         */
        setInstallmentInformation: function (data) {
            data = proceedInstallmentData(data);
            installmentInformation(data);
        }
    };
});
