<?php

namespace GhoSter\KbankPayments\Api\Data;

/**
 * Interface MpiInterface
 * @api
 */
interface MpiInterface
{
    public const  ECI = 'eci';
    public const  XID = 'xid';
    public const  CAVV = 'cavv';
    public const  KBANK_MPI = 'kbank_mpi';

    /**
     * Get Eci
     *
     * @return string
     */
    public function getEci();

    /**
     * Set Eci
     *
     * @param string $eci
     * @return $this
     */
    public function setEci($eci);

    /**
     * Get Xid
     *
     * @return string
     */
    public function getXid();

    /**
     * Set Xid
     *
     * @param string $xid
     * @return $this
     */
    public function setXid($xid);

    /**
     * Get Cavv
     *
     * @return string
     */
    public function getCavv();

    /**
     * Set Cavv
     *
     * @param string $cavv
     * @return $this
     */
    public function setCavv($cavv);

    /**
     * Get Kbank Mpi
     *
     * @return boolean
     */
    public function getKbankMpi();

    /**
     * Set Kbank Mpi
     *
     * @param boolean $kbankMpi
     * @return $this
     */
    public function setKbankMpi($kbankMpi);
}
