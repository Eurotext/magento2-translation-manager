<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\ApiClient;

use Eurotext\RestApiClient\ConfigurationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Configuration implements ConfigurationInterface
{
    const PLUGIN_NAME = 'Magento2 Eurotext Translation Manager';
    const SYSTEM_NAME = 'Magento2';

    const CONFIG_PATH_API_KEY        = 'eurotext/api/key';
    const CONFIG_PATH_API_HOST       = 'eurotext/api/host';
    const CONFIG_PATH_API_DEBUG_MODE = 'eurotext/api/debug_mode';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getApiKey(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_API_KEY);
    }

    public function getHost(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_API_HOST);
    }

    public function getUserAgent(): string
    {
        return \Eurotext\RestApiClient\Configuration::USER_AGENT;
    }

    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }

    public function getPluginVersion(): string
    {
        return '1.0.0'; // @todo get current plugin version
    }

    public function getSystemName(): string
    {
        return self::SYSTEM_NAME;
    }

    public function getSystemVersion(): string
    {
        return '2.2.4'; // @todo get current system version
    }

    public function getDebug(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::CONFIG_PATH_API_DEBUG_MODE);
    }

    public function getDebugFile(): string
    {
        return 'var/log/eurotext_api_debug.log'; // @todo improve file-path generation
    }

}