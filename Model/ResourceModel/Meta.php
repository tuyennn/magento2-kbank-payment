<?php

namespace GhoSter\KbankPayments\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Meta extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('ghoster_kbank_meta', 'meta_id');
        $this->_isPkAutoIncrement = false;
    }

    /**
     * Load by Order Increment Id
     *
     * @param \GhoSter\KbankPayments\Model\Meta $meta
     * @param string $orderIncrementId
     * @return $this
     * @throws LocalizedException
     */
    public function loadByOrderIncrement(\GhoSter\KbankPayments\Model\Meta $meta, $orderIncrementId)
    {
        $connection = $this->getConnection();
        $bind = ['order_id' => $orderIncrementId];
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where(
                'order_id = :order_id'
            )->order('created DESC');

        $metaId = $connection->fetchOne($select, $bind);

        if ($metaId) {
            $this->load($meta, $metaId);
        } else {
            $meta->setData([]);
        }

        return $this;
    }

    /**
     * Load by Charge Id
     *
     * @param \GhoSter\KbankPayments\Model\Meta $meta
     * @param mixed $chargeId
     * @return $this
     * @throws LocalizedException
     */
    public function loadByChargeId(\GhoSter\KbankPayments\Model\Meta $meta, $chargeId): Meta
    {
        $connection = $this->getConnection();
        $bind = ['charge_id' => $chargeId];
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where(
                'charge_id = :charge_id'
            );

        $metaId = $connection->fetchOne($select, $bind);

        if ($metaId) {
            $this->load($meta, $metaId);
        } else {
            $meta->setData([]);
        }

        return $this;
    }
}
