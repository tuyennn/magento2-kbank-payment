<?php
namespace GhoSter\KbankPayments\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use GhoSter\KbankPayments\Gateway\Http\Payload\FilterInterface;

/**
 * Can create a transfer object
 *
 */
class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @var array
     */
    private $payloadFilters;

    /**
     * @param TransferBuilder $transferBuilder
     * @param FilterInterface[] $payloadFilters
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        array $payloadFilters = []
    ) {
        $this->transferBuilder = $transferBuilder;
        $this->payloadFilters = $payloadFilters;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        foreach ($this->payloadFilters as $filter) {
            $request = $filter->filter($request);
        }

        return $this->transferBuilder
            ->setBody($request)
            ->build();
    }
}
