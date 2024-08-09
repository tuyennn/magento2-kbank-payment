<?php

namespace GhoSter\KbankPayments\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class SmartPayId extends ArraySerialized
{
    /**
     * Before saving
     *
     * @return SmartPayId
     */
    public function beforeSave()
    {
        $data = $this->getValue();
        $this->setValue($data);

        return parent::beforeSave();
    }
}
