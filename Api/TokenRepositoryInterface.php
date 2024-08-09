<?php

namespace GhoSter\KbankPayments\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 */
interface TokenRepositoryInterface
{
    /**
     * Save Token
     *
     * @param \GhoSter\KbankPayments\Api\Data\TokenInterface $token
     * @return \GhoSter\KbankPayments\Api\Data\TokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \GhoSter\KbankPayments\Api\Data\TokenInterface $token
    );

    /**
     * Retrieve Token
     *
     * @param int $tokenId
     * @return \GhoSter\KbankPayments\Api\Data\TokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($tokenId);

    /**
     * Retrieve Token
     *
     * @param string $orderIncrementId
     * @return \GhoSter\KbankPayments\Api\Data\TokenInterface
     * @throws LocalizedException
     */
    public function getByOrderIncrement($orderIncrementId);

    /**
     * Retrieve Token
     *
     * @param string $token
     * @return \GhoSter\KbankPayments\Api\Data\TokenInterface
     * @throws LocalizedException
     */
    public function getByToken($token);

    /**
     * Retrieve Token matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \GhoSter\KbankPayments\Api\Data\TokenSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Token
     *
     * @param \GhoSter\KbankPayments\Api\Data\TokenInterface $token
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \GhoSter\KbankPayments\Api\Data\TokenInterface $token
    );

    /**
     * Delete Token by ID
     *
     * @param string $tokenId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tokenId);
}
