<?php
namespace GhoSter\KbankPayments\Api;

/**
 * Interface NotifyManagementInterface
 * @api
 */
interface NotifyManagementInterface
{

    /**
     * Notify from gateway to Magento
     *
     * @return bool
     */
    public function notify(): bool;
}
