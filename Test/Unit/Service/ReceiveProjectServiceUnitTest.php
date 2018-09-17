<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntityReceiverInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Service\Project\FetchProjectEntitiesService;
use Eurotext\TranslationManager\Service\ReceiveProjectService;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class ReceiveProjectServiceUnitTest extends UnitTestAbstract
{
    /** @var ReceiveProjectService */
    private $sut;

    /** @var FetchProjectEntitiesService|\PHPUnit_Framework_MockObject_MockObject */
    private $fetchProjectEntities;

    /** @var ProjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->projectRepository =
            $this->getMockBuilder(ProjectRepositoryInterface::class)
                 ->setMethods(['getById'])
                 ->getMockForAbstractClass();

        $this->fetchProjectEntities =
            $this->getMockBuilder(FetchProjectEntitiesService::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['execute'])
                 ->getMock();

        $this->sut = $this->objectManager->getObject(
            ReceiveProjectService::class,
            [
                'projectRepository'    => $this->projectRepository,
                'fetchProjectEntities' => $this->fetchProjectEntities,
            ]
        );
    }

    public function testItShouldReceiveProject()
    {
        $projectId = 1;

        /** @var ProjectInterface $project */
        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $this->fetchProjectEntities->expects($this->once())->method('execute')->willReturn(
            [EntityReceiverInterface::class => 1]
        );

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
    }

}