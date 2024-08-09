<?php

namespace GhoSter\KbankPayments\Model\ResourceModel\Token;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \GhoSter\KbankPayments\Model\Token::class,
            \GhoSter\KbankPayments\Model\ResourceModel\Token::class
        );
    }
}
