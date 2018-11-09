<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntitySenderInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Service\Project\CreateProjectEntitiesService;
use Eurotext\TranslationManager\Service\Project\CreateProjectEntitiesServiceInterface;
use Eurotext\TranslationManager\Service\Project\CreateProjectService;
use Eurotext\TranslationManager\Service\Project\TransitionProjectService;
use Eurotext\TranslationManager\Service\SendProjectService;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class SendProjectServiceUnitTest extends UnitTestAbstract
{
    /** @var SendProjectService */
    private $sut;

    /** @var CreateProjectService|\PHPUnit_Framework_MockObject_MockObject */
    private $createProject;

    /** @var CreateProjectEntitiesServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $createProjectEntities;

    /** @var ProjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    /** @var TransitionProjectService|\PHPUnit_Framework_MockObject_MockObject */
    private $transitionProject;

    /** @var ProjectStateMachine|\PHPUnit_Framework_MockObject_MockObject */
    private $projectStateMachine;

    protected function setUp()
    {
        parent::setUp();

        $this->projectRepository =
            $this->getMockBuilder(ProjectRepositoryInterface::class)
                 ->setMethods(['getById'])
                 ->getMockForAbstractClass();

        $this->createProject =
            $this->getMockBuilder(CreateProjectService::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['execute'])
                 ->getMock();

        $this->createProjectEntities =
            $this->getMockBuilder(CreateProjectEntitiesService::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['execute'])
                 ->getMock();

        $this->transitionProject =
            $this->getMockBuilder(TransitionProjectService::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['execute'])
                 ->getMock();

        $this->projectStateMachine =
            $this->getMockBuilder(ProjectStateMachine::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['apply'])
                 ->getMock();

        $this->sut = $this->objectManager->getObject(
            SendProjectService::class,
            [
                'projectRepository'     => $this->projectRepository,
                'createProject'         => $this->createProject,
                'createProjectEntities' => $this->createProjectEntities,
                'transitionProject'     => $this->transitionProject,
                'projectStateMachine'   => $this->projectStateMachine,
            ]
        );
    }

    public function testItShouldSendProject()
    {
        $projectId = 1;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->getMockBuilder(Project::class)
                        ->setMethods(['getStatus'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $project->method('getStatus')->willReturn(ProjectInterface::STATUS_TRANSFER);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $this->createProject->expects($this->once())->method('execute')->willReturn(true);

        $this->createProjectEntities->expects($this->once())->method('execute')
                                    ->willReturn([EntitySenderInterface::class => true]);

        $this->transitionProject->expects($this->once())->method('execute')->willReturn(true);

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('bool', $result);
        $this->assertTrue($result);
    }

    public function testItShouldNotSendProjectOnlyForStatusTransfer()
    {
        $this->expectException(InvalidProjectStatusException::class);

        $projectId = 1;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->getMockBuilder(Project::class)
                        ->setMethods(['getStatus'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $project->method('getStatus')->willReturn(ProjectInterface::STATUS_NEW);

        $this->projectRepository->expects($this->once())
                                ->method('getById')->with($projectId)->willReturn($project);

        $this->createProject->expects($this->never())->method('execute');

        $this->createProjectEntities->expects($this->never())->method('execute');

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('bool', $result);
        $this->assertFalse($result);
    }

    public function testItShouldStopOnErrorDuringCreateProject()
    {
        $projectId = 1;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->getMockBuilder(Project::class)
                        ->setMethods(['getStatus'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $project->method('getStatus')->willReturn(ProjectInterface::STATUS_TRANSFER);

        $this->projectRepository->expects($this->once())
                                ->method('getById')->with($projectId)->willReturn($project);

        $this->createProject->expects($this->once())->method('execute')->willReturn(false);

        $this->projectStateMachine->expects($this->once())->method('apply')
                                  ->with($project, ProjectInterface::STATUS_ERROR);

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('bool', $result);
        $this->assertFalse($result);
    }

    public function testItShouldSetStatusErrorWhenOneEntitySenderHasResultFalse()
    {
        $projectId = 1;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->getMockBuilder(Project::class)
                        ->setMethods(['getStatus'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $project->method('getStatus')
                ->willReturnOnConsecutiveCalls(ProjectInterface::STATUS_TRANSFER, ProjectInterface::STATUS_ERROR);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $this->createProject->expects($this->once())->method('execute')->willReturn(true);

        $this->createProjectEntities->expects($this->once())->method('execute')
                                    ->willReturn([EntitySenderInterface::class => false]);

        $this->projectStateMachine->expects($this->once())->method('apply')
                                  ->with($project, ProjectInterface::STATUS_ERROR);

        $this->transitionProject->expects($this->never())->method('execute');

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('bool', $result);
        $this->assertFalse($result);
    }

}