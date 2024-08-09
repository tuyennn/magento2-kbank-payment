define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'kbank_embedded_fullpayment',
                component: 'GhoSter_KbankPayments/js/view/payment/method-renderer/kbank-embedded-fullpayment-method'
            },
            {
                type: 'kbank_embedded_installment',
                component: 'GhoSter_KbankPayments/js/view/payment/method-renderer/kbank-embedded-installment-method'
            }
        );
        return Component.extend({});
    }
);
