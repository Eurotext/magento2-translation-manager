<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Builder;

use Eurotext\RestApiClient\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationMockBuilder
{
    /**
     * @var \PHPUnit\Framework\TestCase
     */
    private $testCase;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    public function buildConfiguration(): \PHPUnit_Framework_MockObject_MockObject
    {
        $config = $this->testCase
            ->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->setMethods(['getApiKey', 'getHost', 'getDebug'])
            ->getMock();

        $config->method('getApiKey')->willReturn(\constant('EUROTEXT_API_KEY'));
        $config->method('getHost')->willReturn('https://sandbox.api.eurotext.de');
        $config->method('getDebug')->willReturn(false);

        return $config;
    }
}