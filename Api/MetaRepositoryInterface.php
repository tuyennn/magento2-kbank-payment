<?php

namespace GhoSter\KbankPayments\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 */
interface MetaRepositoryInterface
{
    /**
     * Save Meta
     *
     * @param \GhoSter\KbankPayments\Api\Data\MetaInterface $meta
     * @return \GhoSter\KbankPayments\Api\Data\MetaInterface
     * @throws LocalizedException
     */
    public function save(
        \GhoSter\KbankPayments\Api\Data\MetaInterface $meta
    );

    /**
     * Retrieve Meta
     *
     * @param int $metaId
     * @return \GhoSter\KbankPayments\Api\Data\MetaInterface
     * @throws LocalizedException
     */
    public function getById($metaId);

    /**
     * Retrieve Meta
     *
     * @param string $orderIncrementId
     * @return \GhoSter\KbankPayments\Api\Data\MetaInterface
     * @throws LocalizedException
     */
    public function getByOrderIncrement($orderIncrementId);

    /**
     * Retrieve Meta
     *
     * @param string $chargeId
     * @return \GhoSter\KbankPayments\Api\Data\MetaInterface
     * @throws LocalizedException
     */
    public function getByChargeId($chargeId);

    /**
     * Retrieve Meta matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \GhoSter\KbankPayments\Api\Data\MetaSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Meta
     *
     * @param \GhoSter\KbankPayments\Api\Data\MetaInterface $meta
     * @return bool
     * @throws LocalizedException
     */
    public function delete(
        \GhoSter\KbankPayments\Api\Data\MetaInterface $meta
    );

    /**
     * Delete Meta by ID
     *
     * @param int $metaId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($metaId);
}
