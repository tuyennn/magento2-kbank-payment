<?php
namespace GhoSter\KbankPayments\Model\Adminhtml\Source;

use Magento\Payment\Model\Source\Cctype as PaymentCctype;

class Cctype extends PaymentCctype
{
    /**
     * @inheritdoc
     */
    public function getAllowedTypes()
    {
        return ['VI', 'MC', 'AE', 'DI', 'JCB', 'DN'];
    }
}
