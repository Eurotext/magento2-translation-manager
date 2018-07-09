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

class ConfigurationFactory
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

    public function create(): ConfigurationInterface
    {
        $configuration = new \Eurotext\RestApiClient\Configuration();
        $configuration->setApiKey((string)$this->scopeConfig->getValue(self::CONFIG_PATH_API_KEY));
        $configuration->setHost((string)$this->scopeConfig->getValue(self::CONFIG_PATH_API_HOST));

        $configuration->setDebug((bool)$this->scopeConfig->getValue(self::CONFIG_PATH_API_DEBUG_MODE));
        $configuration->setDebugFile('var/log/eurotext_api_debug.log'); // @todo improve file-path generation

        $configuration->setPluginName(self::PLUGIN_NAME);
        $configuration->setPluginVersion('1.0.0'); // @todo get current plugin version

        $configuration->setSystemName(self::SYSTEM_NAME); // @todo get complete Magento Name incl. Dist Type
        $configuration->setSystemVersion('2.2.4'); // @todo get current system version

    }

}