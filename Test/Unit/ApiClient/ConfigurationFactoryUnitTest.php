<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\ApiClient;

use Eurotext\RestApiClient\ConfigurationInterface;
use Eurotext\TranslationManager\ApiClient\ConfigurationFactory;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;

class ConfigurationFactoryUnitTest extends UnitTestAbstract
{
    /** @var ConfigurationFactory */
    private $sut;

    /** @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $scopeConfig;

    /** @var ModuleListInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $moduleList;

    /** @var ProductMetadataInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $magentoMetadata;

    protected function setUp()
    {
        parent::setUp();

        $this->scopeConfig     = $this->createMock(ScopeConfigInterface::class);
        $this->moduleList      = $this->createMock(ModuleListInterface::class);
        $this->magentoMetadata = $this->createMock(ProductMetadataInterface::class);

        $this->sut = $this->objectManager->getObject(
            ConfigurationFactory::class,
            [
                'scopeConfig'     => $this->scopeConfig,
                'moduleList'      => $this->moduleList,
                'magentoMetadata' => $this->magentoMetadata,
            ]
        );
    }

    public function testItShouldReturnAConfigurationObject()
    {
        $apikey = 'api-key-value';
        $host   = 'host-name-value';
        $debug  = true;

        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $scopeCode = null;

        $valueMap = [
            [ConfigurationFactory::CONFIG_PATH_API_KEY, $scopeType, $scopeCode, $apikey],
            [ConfigurationFactory::CONFIG_PATH_API_HOST, $scopeType, $scopeCode, $host],
            [ConfigurationFactory::CONFIG_PATH_API_DEBUG_MODE, $scopeType, $scopeCode, $debug],
        ];
        $this->scopeConfig->method('getValue')->willReturnMap($valueMap);

        $this->moduleList->expects($this->once())->method('getOne')->willReturn(['setup_version' => '1.0.0']);

        $this->magentoMetadata->expects($this->once())->method('getName')->willReturn('Magento');
        $this->magentoMetadata->expects($this->once())->method('getEdition')->willReturn('Community');
        $this->magentoMetadata->expects($this->once())->method('getVersion')->willReturn('2.2.6');

        $configuration = $this->sut->create();

        $this->assertInstanceOf(ConfigurationInterface::class, $configuration);

        $this->assertEquals($apikey, $configuration->getApiKey());
        $this->assertEquals($host, $configuration->getHost());
        $this->assertEquals($debug, $configuration->getDebug());
    }
}