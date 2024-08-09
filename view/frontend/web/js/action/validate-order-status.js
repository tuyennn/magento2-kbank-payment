define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage'
    ],
    function (
        ko,
        $,
        urlBuilder,
        storage
    ) {
        'use strict';

        return function (orderId, deferred) {
            var serviceUrl;

            deferred = deferred || $.Deferred();

            serviceUrl = urlBuilder.createUrl('/kbank/:orderId/validate-order-status', {
                orderId: orderId
            });

            return storage.get(
                serviceUrl,
                false
            ).done(function (response) {
                deferred.resolve();
            }).fail(function (response) {
                deferred.reject();
            });
        };
    }
);
