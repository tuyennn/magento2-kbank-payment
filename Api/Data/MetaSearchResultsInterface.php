<?php

namespace GhoSter\KbankPayments\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface MetaSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Meta list.
     *
     * @return \GhoSter\KbankPayments\Api\Data\MetaInterface[]
     */
    public function getItems();

    /**
     * Set meta list.
     *
     * @param \GhoSter\KbankPayments\Api\Data\MetaInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
