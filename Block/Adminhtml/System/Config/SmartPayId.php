<?php

namespace GhoSter\KbankPayments\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use GhoSter\KbankPayments\Block\Adminhtml\System\Config\SmartPayId\Activation;

class SmartPayId extends AbstractFieldArray
{
    /**
     * @var Activation
     */
    protected $activation;

    /**
     * Prepare to render
     *
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'smartpay_id',
            [
                'label' => __('SMARTPAY ID'),
                'renderer' => false
            ]
        );
        $this->addColumn(
            'payment_term',
            [
                'label' => __('PAYMENT TERM'),
                'renderer' => false
            ]
        );
        $this->addColumn(
            'installment_title',
            [
                'label' => __('Installment Title'),
                'renderer' => false
            ]
        );
        $this->addColumn(
            'status',
            [
                'label' => __('Status'),
                'renderer' => $this->_getActivationRenderer()
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Installment');
    }

    /**
     * Get activation options.
     *
     * @return Activation
     * @throws LocalizedException
     */
    protected function _getActivationRenderer(): Activation
    {
        if (!$this->activation) {
            $this->activation = $this->getLayout()->createBlock(
                Activation::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->activation;
    }

    /**
     * Prepare existing row data object.
     *
     * @param DataObject $row
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $customAttribute = $row->getData('status');

        $key = 'option_' . $this->_getActivationRenderer()->calcOptionHash($customAttribute);
        $options[$key] = 'selected = "selected"';
        $row->setData('option_extra_attrs', $options);
    }
}
