<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Builder;

use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Api\ProjectSeederInterface;
use Eurotext\TranslationManager\Model\ProjectFactory;
use PHPUnit\Framework\TestCase;

class ProjectMockBuilder
{
    /**
     * @var \PHPUnit\Framework\TestCase
     */
    private $testCase;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function buildProjectSeederMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ProjectSeederInterface::class)
            ->setMethods(['seed'])
            ->getMockForAbstractClass();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function buildProjectRepositoryMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ProjectRepositoryInterface::class)
            ->setMethods(['save'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function buildProjectFactoryMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ProjectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockBuilder($className): \PHPUnit_Framework_MockObject_MockBuilder
    {
        return new \PHPUnit_Framework_MockObject_MockBuilder($this->testCase, $className);
    }

}