<?php
namespace GhoSter\KbankPayments\Gateway\Http\Payload\Filter;

use GhoSter\KbankPayments\Gateway\Http\Payload\FilterInterface;

/**
 * Removes a set of fields from the payload
 */
class RemoveFieldsFilter implements FilterInterface
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
     */
    public function filter(array $data)
    {
        foreach ($this->fields as $field) {
            unset($data[$field]);
        }

        return $data;
    }
}
