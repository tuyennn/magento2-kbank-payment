<?php

namespace GhoSter\KbankPayments\Block\Adminhtml\System\Config\SmartPayId;

use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class Activation extends Select
{
    /**
     * @var Enabledisable
     */
    protected $enableDisable;

    /**
     * Activation constructor.
     *
     * @param Context $context
     * @param Enabledisable $enableDisable
     * @param array $data
     */
    public function __construct(
        Context $context,
        Enabledisable $enableDisable,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->enableDisable = $enableDisable;
    }

    /**
     * Set Input Name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Parse to html.
     *
     * @return mixed
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $attributes = $this->enableDisable->toOptionArray();

            foreach ($attributes as $attribute) {
                $this->addOption($attribute['value'], $attribute['label']);
            }
        }

        return parent::_toHtml();
    }
}
