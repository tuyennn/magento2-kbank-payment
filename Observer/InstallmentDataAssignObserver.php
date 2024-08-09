<?php
namespace GhoSter\KbankPayments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Adds the payment info to the payment object
 *
 */
class InstallmentDataAssignObserver extends MainDataAssignObserver
{
    /**
     * @var array
     */
    protected $additionalInformationList = [
        'smartpay_id',
        'term',
        'terminal_id',
        'tokenValue',
        'cardBrand',
        'cardExpYear',
        'cardExpMonth',
        'mid'
    ];
}
