<?php

namespace GhoSter\KbankPayments\Logger;

use GhoSter\KbankPayments\Gateway\Config;
use Magento\Framework\App\ObjectManager;

class Logger extends \Monolog\Logger
{
    /**
     * @var Config
     */
    public $config;

    /**
     * Checks whether the Logger has a handler that listens on the given level
     *
     * @param int $level
     * @return bool
     */
    public function isHandling($level): bool
    {
        return $this->getConfig()->isDebugMode() && parent::isHandling($level);
    }

    /**
     * Get configuration
     *
     * @return Config
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $this->config = ObjectManager::getInstance()->get(Config::class);
        }

        return $this->config;
    }

    /**
     * This method allows for compatibility with common interfaces.
     *
     * @param string|array $message
     * @param array $context
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        if (is_array($message)) {
            $message = var_export($message, true);
        }

        parent::debug($message, $context);
    }

    /**
     * This method allows for compatibility with common interfaces.
     *
     * @param string|array $message
     * @param array $context
     * @return void
     */
    public function info($message, array $context = []): void
    {
        if (is_array($message)) {
            $message = var_export($message, true);
        }

        parent::info($message, $context);
    }
}
