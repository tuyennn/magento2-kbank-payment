<?php

namespace GhoSter\KbankPayments\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use GhoSter\KbankPayments\Api\Data\MpiInterface;

class Mpi extends AbstractSimpleObject implements MpiInterface
{
    /**
     * @inheritDoc
     */
    public function getEci()
    {
        return $this->_get(self::ECI);
    }

    /**
     * @inheritDoc
     */
    public function setEci($eci)
    {
        return $this->setData(self::ECI, $eci);
    }

    /**
     * @inheritDoc
     */
    public function getXid()
    {
        return $this->_get(self::XID);
    }

    /**
     * @inheritDoc
     */
    public function setXid($xid)
    {
        return $this->setData(self::XID, $xid);
    }

    /**
     * @inheritDoc
     */
    public function getCavv()
    {
        return $this->_get(self::CAVV);
    }

    /**
     * @inheritDoc
     */
    public function setCavv($cavv)
    {
        return $this->setData(self::CAVV, $cavv);
    }

    /**
     * @inheritDoc
     */
    public function getKbankMpi()
    {
        return $this->_get(self::KBANK_MPI);
    }

    /**
     * @inheritDoc
     */
    public function setKbankMpi($kbankMpi)
    {
        return $this->setData(self::KBANK_MPI, $kbankMpi);
    }
}
