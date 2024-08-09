<?php
namespace GhoSter\KbankPayments\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PaymentAction implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'authorize',
                'label' => __('Authorize Only'),
            ],
            [
                'value' => 'authorize_capture',
                'label' => __('Authorize and Capture')
            ]
        ];
    }
}
