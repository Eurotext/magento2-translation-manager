<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntityRetrieverInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Service\Project\FetchProjectEntitiesServiceInterface;
use Eurotext\TranslationManager\Service\Project\TransitionProjectService;
use Eurotext\TranslationManager\Service\Project\TransitionProjectServiceInterface;
use Eurotext\TranslationManager\Service\RetrieveProjectService;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class RetrieveProjectServiceUnitTest extends UnitTestAbstract
{
    /** @var ProjectStateMachine|\PHPUnit_Framework_MockObject_MockObject */
    private $projectStateMachine;

    /** @var RetrieveProjectService */
    private $sut;

    /** @var FetchProjectEntitiesServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $fetchProjectEntities;

    /** @var TransitionProjectServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $transitionProject;

    /** @var ProjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectRepository    = $this->createMock(ProjectRepositoryInterface::class);
        $this->fetchProjectEntities = $this->createMock(FetchProjectEntitiesServiceInterface::class);
        $this->projectStateMachine  = $this->createMock(ProjectStateMachine::class);
        $this->transitionProject    = $this->createMock(TransitionProjectService::class);

        $this->sut = $this->objectManager->getObject(
            RetrieveProjectService::class,
            [
                'projectRepository'    => $this->projectRepository,
                'projectStateMachine'  => $this->projectStateMachine,
                'fetchProjectEntities' => $this->fetchProjectEntities,
                'transitionProject'    => $this->transitionProject,
            ]
        );
    }

    public function testItShouldRetrieveProject()
    {
        $projectId = 1;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->createMock(ProjectInterface::class);
        $project->method('getStatus')->willReturn(ProjectInterface::STATUS_ACCEPTED);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $this->projectStateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($project, ProjectInterface::STATUS_IMPORTED);

        $this->fetchProjectEntities->expects($this->once())->method('execute')->willReturn(
            [EntityRetrieverInterface::class => true]
        );

        // ETM2-33: the final transition has been disabled since it is not supported right now
        $this->transitionProject->expects($this->never())->method('execute')->willReturn(true);

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('bool', $result);
        $this->assertTrue($result);
    }

    public function testItShouldNotRetrieveProjectForStatusOtherThanAccepted()
    {
        $this->expectException(InvalidProjectStatusException::class);

        $projectId = 1;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->createMock(ProjectInterface::class);
        $project->method('getStatus')->willReturn(ProjectInterface::STATUS_EXPORTED);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $this->projectStateMachine->expects($this->never())->method('apply');

        $this->fetchProjectEntities->expects($this->never())->method('execute');

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('bool', $result);
        $this->assertFalse($result);
    }

    public function testItShouldQuitForProjectStatusError()
    {
        $projectId = 1;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->createMock(ProjectInterface::class);
        $project->method('getStatus')
                ->willReturnOnConsecutiveCalls(ProjectInterface::STATUS_ACCEPTED, ProjectInterface::STATUS_ERROR);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $this->projectStateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($project, ProjectInterface::STATUS_ERROR);

        $this->fetchProjectEntities->expects($this->once())->method('execute')->willReturn(
            [EntityRetrieverInterface::class => false]
        );

        $this->transitionProject->expects($this->never())->method('execute');

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('bool', $result);
        $this->assertFalse($result);
    }
}
