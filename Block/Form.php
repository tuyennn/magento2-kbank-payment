<?php

namespace GhoSter\KbankPayments\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Payment\Model\MethodInterface;

/**
 * Block for representing the payment form
 *
 */
class Form extends Template
{
    /**
     * Retrieve payment method model
     *
     * @return MethodInterface
     * @throws LocalizedException
     */
    public function getMethod()
    {
        $method = $this->getData('method');

        if (!$method instanceof MethodInterface) {
            throw new LocalizedException(
                __('We cannot retrieve the payment method model object.')
            );
        }
        return $method;
    }

    /**
     * Sets payment method instance to form
     *
     * @param MethodInterface $method
     * @return Form
     */
    public function setMethod(MethodInterface $method)
    {
        $this->setData('method', $method);
        return $this;
    }

    /**
     * Retrieve payment method code
     *
     * @return string
     * @throws LocalizedException
     */
    public function getMethodCode()
    {
        return $this->getMethod()->getCode();
    }

    /**
     * Retrieve field value data from payment info object
     *
     * @param mixed $field
     * @return string
     * @throws LocalizedException
     */
    public function getInfoData($field)
    {
        return $this->escapeHtml($this->getMethod()->getInfoInstance()->getData($field));
    }
}
