<?php
namespace GhoSter\KbankPayments\Gateway\Http\Payload;

/**
 * Describes a filter for filtering content after all the builders have finished
 *
 */
interface FilterInterface
{
    /**
     * Filters some data before use
     *
     * @param array $data
     * @return array
     */
    public function filter(array $data);
}
