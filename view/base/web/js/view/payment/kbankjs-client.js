define([
    'jquery',
    'uiClass',
    'GhoSter_KbankPayments/js/view/payment/validator-handler'
    //phpcs:ignore Squiz.Functions.MultiLineFunctionDeclaration.SpaceBeforeOpenParen
], function (
    $,
    Class,
    validatorHandler
) {
    'use strict';

    return Class.extend({
        defaults: {
            environment: 'production'
        },

        /**
         * @{inheritdoc}
         */
        initialize: function () {
            validatorHandler.initialize();

            this._super();

            return this;
        },

        /**
         * Creates the token pair with the provided data
         *
         * @param {Object} data
         * @return {jQuery.Deferred}
         */
        createTokens: function (data) {
            var deferred = $.Deferred();

            this._createTokens(deferred, data);

            return deferred.promise();
        },

        /**
         * Creates a token from the payment information in the form
         *
         * @param {jQuery.Deferred} deferred
         * @param {Object} data
         */
        _createTokens: async function (deferred, data) {
            if (window.hasOwnProperty('KInlineCheckout') && typeof window.KInlineCheckout !== 'undefined') {

                let token = await window.KInlineCheckout.getToken()
                    .then((result) => {
                        return result;
                    });

                validatorHandler.validate(token, function (valid, messages) {
                    if (valid) {
                        deferred.resolve({
                            tokenValue: token['token'],
                            cardData: token.hasOwnProperty('card') ? token['card'] : null
                        });
                    } else {
                        deferred.reject(messages);
                    }
                });
            } else {
                deferred.reject(['The payment gateway was not ready.']);
            }
        }
    });
});
