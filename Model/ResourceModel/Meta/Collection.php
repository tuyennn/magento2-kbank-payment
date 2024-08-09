<?php

namespace GhoSter\KbankPayments\Model\ResourceModel\Meta;

use GhoSter\KbankPayments\Model\Meta;
use GhoSter\KbankPayments\Model\ResourceModel\Meta as ResourceMeta;
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
            Meta::class,
            ResourceMeta::class
        );
    }
}
