<?php

namespace GhoSter\KbankPayments\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Token extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('ghoster_kbank_token', 'token_id');
    }

    /**
     * Load by Order Increment Id
     *
     * @param \GhoSter\KbankPayments\Model\Token $token
     * @param string $orderIncrementId
     * @return $this
     * @throws LocalizedException
     */
    public function loadByOrderIncrement(\GhoSter\KbankPayments\Model\Token $token, string $orderIncrementId): Token
    {
        $connection = $this->getConnection();
        $bind = ['order_id' => $orderIncrementId];
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where(
                'order_id = :order_id'
            )->order('created DESC');

        $tokenId = $connection->fetchOne($select, $bind);

        if ($tokenId) {
            $this->load($token, $tokenId);
        } else {
            $token->setData([]);
        }

        return $this;
    }

    /**
     * Load by Token
     *
     * @param \GhoSter\KbankPayments\Model\Token $token
     * @param string $tokenValue
     * @return $this
     * @throws LocalizedException
     */
    public function loadByToken(\GhoSter\KbankPayments\Model\Token $token, string $tokenValue): Token
    {
        $connection = $this->getConnection();
        $bind = ['token' => $tokenValue];
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where(
                'token = :token'
            );

        $tokenId = $connection->fetchOne($select, $bind);

        if ($tokenId) {
            $this->load($token, $tokenId);
        } else {
            $token->setData([]);
        }

        return $this;
    }
}
