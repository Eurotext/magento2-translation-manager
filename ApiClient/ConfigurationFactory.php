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
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;

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

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ProductMetadataInterface
     */
    private $magentoMetadata;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ModuleListInterface $moduleList,
        ProductMetadataInterface $magentoMetadata
    ) {
        $this->scopeConfig     = $scopeConfig;
        $this->moduleList      = $moduleList;
        $this->magentoMetadata = $magentoMetadata;
    }

    public function create(): ConfigurationInterface
    {
        $apiKey = (string)$this->scopeConfig->getValue(self::CONFIG_PATH_API_KEY);
        if (empty($apiKey)) {
            $msg = sprintf('invalid eurotext api-key. Please set your API-Key in the configuration');
            throw new \InvalidArgumentException($msg);
        }

        $host = (string)$this->scopeConfig->getValue(self::CONFIG_PATH_API_HOST);
        if (empty($apiKey)) {
            $msg = sprintf('invalid eurotext api-host. Please set your API-Host in the configuration');
            throw new \InvalidArgumentException($msg);
        }

        $configuration = new \Eurotext\RestApiClient\Configuration();

        // API
        $configuration->setApiKey($apiKey);
        $configuration->setHost($host);

        // DEBUG
        $configuration->setDebug((bool)$this->scopeConfig->getValue(self::CONFIG_PATH_API_DEBUG_MODE));
        $configuration->setDebugFile('var/log/eurotext_api_debug.log'); // @todo improve file-path generation

        // Module
        $moduleData    = $this->moduleList->getOne('Eurotext_TranslationManager');
        $pluginVersion = $moduleData['setup_version'];
        $configuration->setPluginVersion($pluginVersion);
        $configuration->setPluginName(self::PLUGIN_NAME);

        // System
        $systemName    = $this->magentoMetadata->getName() . ' ' . $this->magentoMetadata->getEdition();
        $systemVersion = $this->magentoMetadata->getVersion();
        $configuration->setSystemName($systemName);
        $configuration->setSystemVersion($systemVersion);

        return $configuration;
    }

}