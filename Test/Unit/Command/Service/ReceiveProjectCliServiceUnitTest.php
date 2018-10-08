<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command\Service;

use Eurotext\RestApiClient\Validator\ProjectStatusValidator;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Command\Service\ReceiveProjectCliService;
use Eurotext\TranslationManager\Service\ReceiveProjectService;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class ReceiveProjectCliServiceUnitTest extends UnitTestAbstract
{
    /** @var ReceiveProjectCliService */
    protected $sut;

    /** @var ReceiveProjectService|\PHPUnit_Framework_MockObject_MockObject */
    protected $receiveProject;

    /** @var ProjectRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectRepository;

    /** @var ProjectStatusValidator|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectStatusValidator;

    /** @var ProjectStateMachine|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectStateMachine;

    protected function setUp()
    {
        parent::setUp();

        $this->receiveProject =
            $this->getMockBuilder(ReceiveProjectService::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['execute'])
                 ->getMock();

        $this->projectRepository =
            $this->getMockBuilder(ProjectRepositoryInterface::class)
                 ->getMock();

        $this->projectStatusValidator =
            $this->getMockBuilder(ProjectStatusValidator::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['validate'])
                 ->getMock();

        $this->projectStateMachine =
            $this->getMockBuilder(ProjectStateMachine::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['apply'])
                 ->getMock();

        $this->sut = $this->objectManager->getObject(
            ReceiveProjectCliService::class,
            [
                'receiveProject'         => $this->receiveProject,
                'projectRepository'      => $this->projectRepository,
                'projectStatusValidator' => $this->projectStatusValidator,
                'projectStateMachine'    => $this->projectStateMachine,
            ]
        );
    }

    /**
     * @throws \Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException
     * @throws \Eurotext\TranslationManager\Exception\InvalidProjectStatusException
     */
    public function testItShouldReceiveAProject()
    {
        $projectId = 1;

        $project = $this->getMockBuilder(ProjectInterface::class)->getMock();
        /** @var ProjectInterface $project */

        $this->projectRepository->expects($this->once())->method('getById')->willReturn($project);

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(true);

        $this->projectStateMachine
            ->expects($this->exactly(2))->method('apply');
            //->withConsecutive(ProjectInterface::STATUS_TRANSLATED, ProjectInterface::STATUS_ACCEPTED);

        $this->receiveProject->expects($this->once())->method('execute')->willReturn(true);

        $result = $this->sut->executeById($projectId);

        $this->assertTrue($result);
    }

    /**
     * @throws \Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException
     * @throws \Eurotext\TranslationManager\Exception\InvalidProjectStatusException
     */
    public function testItShouldNotChangeStatusIfNotAllItemsAreFinished()
    {
        $projectId = 1;

        $project = $this->getMockBuilder(ProjectInterface::class)->getMock();
        /** @var ProjectInterface $project */

        $this->projectRepository->expects($this->once())->method('getById')->willReturn($project);

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(false);

        $this->projectStateMachine->expects($this->never())->method('apply');

        $this->receiveProject->expects($this->never())->method('execute');

        $result = $this->sut->executeById($projectId);

        $this->assertFalse($result);
    }

}
