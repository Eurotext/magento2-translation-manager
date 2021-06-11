<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Builder;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntitySeederInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
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
     * @return ProjectInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function buildProjectMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getMockBuilder(ProjectInterface::class)
                    ->setMethods(['getProjectId'])
                    ->getMockForAbstractClass();
    }

    /**
     * @return EntitySeederInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function buildProjectSeederMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getMockBuilder(EntitySeederInterface::class)
                    ->setMethods(['seed'])
                    ->getMockForAbstractClass();
    }

    /**
     * @return ProjectRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function buildProjectRepositoryMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getMockBuilder(ProjectRepositoryInterface::class)
                    ->setMethods(['save', 'getList'])
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass();
    }

    /**
     * @return ProjectFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    public function buildProjectFactoryMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getMockBuilder(ProjectFactory::class)
                    ->setMethods(['create'])
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    protected function getMockBuilder($className): \PHPUnit\Framework\MockObject\MockBuilder
    {
        return new \PHPUnit\Framework\MockObject\MockBuilder($this->testCase, $className);
    }

}
