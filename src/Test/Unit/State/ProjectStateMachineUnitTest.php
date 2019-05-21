<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\State;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class ProjectStateMachineUnitTest extends UnitTestAbstract
{
    /** @var ProjectStateMachine */
    private $sut;

    /** @var ProjectRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->sut = new ProjectStateMachine($this->projectRepository);
    }

    /**
     * @dataProvider provideAllowedStatusTransitions
     *
     * @param string $currentStatus
     * @param string $newStatus
     *
     * @throws IllegalProjectStatusChangeException
     * @throws InvalidProjectStatusException
     */
    public function testAllowedStatusTransitions(string $currentStatus, string $newStatus)
    {
        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->createMock(ProjectInterface::class);
        $project->expects($this->once())->method('getStatus')->willReturn($currentStatus);
        $project->expects($this->once())->method('setStatus')->with($newStatus);

        $this->projectRepository->expects($this->once())->method('save');

        $this->sut->apply($project, $newStatus);
    }

    public function provideAllowedStatusTransitions()
    {
        $data = [];

        /** @var ProjectRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject $projectRepository */
        $projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $projectStateMachine = new ProjectStateMachine($projectRepository);

        $allowedTransitions = $projectStateMachine->getAllowedTransitions();

        foreach ($allowedTransitions as $formStatus => $toStatusAllowed) {
            $currentStatus = $formStatus;

            foreach ($toStatusAllowed as $toStatus) {
                $newStatus = $toStatus;

                $data[] = ['currentStatus' => $currentStatus, 'newStatus' => $newStatus];
            }
        }

        return $data;
    }

    public function testIllegalStatusTransitions()
    {
        $this->expectException(IllegalProjectStatusChangeException::class);

        $currentStatus = ProjectInterface::STATUS_NEW;
        $newStatus     = ProjectInterface::STATUS_IMPORTED;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->createMock(ProjectInterface::class);
        $project->expects($this->once())->method('getStatus')->willReturn($currentStatus);
        $project->expects($this->never())->method('setStatus');

        $this->projectRepository->expects($this->never())->method('save');

        $this->sut->apply($project, $newStatus);
    }

    public function testInvalidStatus()
    {
        $this->expectException(InvalidProjectStatusException::class);

        $currentStatus = 'UNKNOWN STATUS';
        $newStatus     = ProjectInterface::STATUS_EXPORTED;

        /** @var ProjectInterface|\PHPUnit_Framework_MockObject_MockObject $project */
        $project = $this->createMock(ProjectInterface::class);
        $project->expects($this->once())->method('getStatus')->willReturn($currentStatus);
        $project->expects($this->never())->method('setStatus');

        $this->projectRepository->expects($this->never())->method('save');

        $this->sut->apply($project, $newStatus);
    }
}