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

class ConfigurationFactoryUnitTest extends UnitTestAbstract
{
    /** @var ConfigurationFactory */
    private $sut;

    /** @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $scopeConfig;

    protected function setUp()
    {
        parent::setUp();

        $this->scopeConfig =
            $this->getMockBuilder(ScopeConfigInterface::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['getValue'])
                 ->getMockForAbstractClass();

        $this->sut = new ConfigurationFactory($this->scopeConfig);
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
        $this->scopeConfig->expects($this->any())
                          ->method('getValue')
                          ->willReturnMap($valueMap);
        // ->willReturnOnConsecutiveCalls($apikey, $host, $debug);

        $configuration = $this->sut->create();

        $this->assertInstanceOf(ConfigurationInterface::class, $configuration);

        $this->assertEquals($apikey, $configuration->getApiKey());
        $this->assertEquals($host, $configuration->getHost());
        $this->assertEquals($debug, $configuration->getDebug());
    }
}