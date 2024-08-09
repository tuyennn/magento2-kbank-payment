<?php

namespace GhoSter\KbankPayments\Logger;

use Monolog\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/kbank_payment.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
