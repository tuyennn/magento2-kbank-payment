<?php

namespace GhoSter\KbankPayments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class MainDataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @var array
     */
    protected $additionalInformationList = [
        'term',
        'tokenValue',
        'cardBrand',
        'cardExpYear',
        'cardExpMonth',
        'mid'
    ];

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
