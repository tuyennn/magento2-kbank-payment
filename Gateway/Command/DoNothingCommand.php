<?php

namespace GhoSter\KbankPayments\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;

class DoNothingCommand implements CommandInterface
{

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject) // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
    {
    }
}
