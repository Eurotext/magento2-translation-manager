<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\ApiClient;

use Eurotext\TranslationManager\ApiClient\ConfigurationFactory;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;
use Magento\Framework\App\Config\Storage\Writer as StorageWriter;

class ConfigurationFactoryIntegrationTest extends IntegrationTestAbstract
{
    /** @var ConfigurationFactory */
    private $sut;

    /** @var StorageWriter */
    private $configWriter;

    protected function setUp()
    {
        parent::setUp();

        $this->configWriter = $this->objectManager->get(StorageWriter::class);

        $this->sut = $this->objectManager->get(ConfigurationFactory::class);
    }

    public function testConfiguration()
    {
        $apiKey = '1234567890';
        $this->configWriter->save(ConfigurationFactory::CONFIG_PATH_API_KEY, $apiKey);

        $configuration = $this->sut->create();

        $this->assertNotEmpty($configuration->getApiKey());
        $this->assertEquals($apiKey, $configuration->getApiKey());
        $this->assertNotEmpty($configuration->getHost());
        $this->assertNotEmpty($configuration->getPluginVersion());
        $this->assertNotEmpty($configuration->getSystemName());
        $this->assertNotEmpty($configuration->getSystemVersion());
    }
}