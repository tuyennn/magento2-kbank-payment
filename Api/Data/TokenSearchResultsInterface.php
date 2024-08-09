<?php

namespace GhoSter\KbankPayments\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface TokenSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Token list.
     *
     * @return \GhoSter\KbankPayments\Api\Data\TokenInterface[]
     */
    public function getItems();

    /**
     * Set token list.
     *
     * @param \GhoSter\KbankPayments\Api\Data\TokenInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
