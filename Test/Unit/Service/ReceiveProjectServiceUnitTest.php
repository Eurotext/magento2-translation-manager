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
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Service\Project\FetchProjectEntitiesService;
use Eurotext\TranslationManager\Service\ReceiveProjectService;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class ReceiveProjectServiceUnitTest extends UnitTestAbstract
{
    /** @var ProjectStateMachine|\PHPUnit_Framework_MockObject_MockObject */
    private $projectStateMachine;

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

        $this->projectStateMachine =
            $this->getMockBuilder(ProjectStateMachine::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['apply'])
                 ->getMock();

        $this->sut = $this->objectManager->getObject(
            ReceiveProjectService::class,
            [
                'projectRepository'    => $this->projectRepository,
                'projectStateMachine'  => $this->projectStateMachine,
                'fetchProjectEntities' => $this->fetchProjectEntities,
            ]
        );
    }

    public function testItShouldReceiveProject()
    {
        $projectId = 1;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->getMockBuilder(Project::class)
                        ->setMethods(['getStatus'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $project->method('getStatus')->willReturn(ProjectInterface::STATUS_ACCEPTED);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $this->projectStateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($project, ProjectInterface::STATUS_IMPORTED);

        $this->fetchProjectEntities->expects($this->once())->method('execute')->willReturn(
            [EntityReceiverInterface::class => true]
        );

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('bool', $result);
        $this->assertTrue($result);
    }

    public function testItShouldNotReceiveProjectForStatusOtherThanAccepted()
    {
        $this->expectException(InvalidProjectStatusException::class);

        $projectId = 1;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->getMockBuilder(Project::class)
                        ->setMethods(['getStatus'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $project->method('getStatus')->willReturn(ProjectInterface::STATUS_EXPORTED);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $this->projectStateMachine->expects($this->never())->method('apply');

        $this->fetchProjectEntities->expects($this->never())->method('execute');

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('bool', $result);
        $this->assertFalse($result);
    }

}