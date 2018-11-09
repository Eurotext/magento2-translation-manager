<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Console\Service;

use Eurotext\RestApiClient\Validator\ProjectStatusValidator;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Console\Service\RetrieveProjectCliService;
use Eurotext\TranslationManager\Service\RetrieveProjectServiceInterface;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class RetrieveProjectCliServiceUnitTest extends UnitTestAbstract
{
    /** @var RetrieveProjectCliService */
    protected $sut;

    /** @var RetrieveProjectServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $retrieveProject;

    /** @var ProjectRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectRepository;

    /** @var ProjectStatusValidator|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectStatusValidator;

    /** @var ProjectStateMachine|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectStateMachine;

    protected function setUp()
    {
        parent::setUp();

        $this->retrieveProject =
            $this->getMockBuilder(RetrieveProjectServiceInterface::class)
                 ->setMethods(['execute'])
                 ->getMockForAbstractClass();

        $this->projectRepository =
            $this->getMockBuilder(ProjectRepositoryInterface::class)
                 ->getMockForAbstractClass();

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
            RetrieveProjectCliService::class,
            [
                'retrieveProject'         => $this->retrieveProject,
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
    public function testItShouldRetrieveAProject()
    {
        $projectId = 1;

        $project = $this->getMockBuilder(ProjectInterface::class)->getMock();
        /** @var ProjectInterface $project */

        $this->projectRepository->expects($this->once())->method('getById')->willReturn($project);

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(true);

        $this->projectStateMachine
            ->expects($this->exactly(2))->method('apply');
        //->withConsecutive(ProjectInterface::STATUS_TRANSLATED, ProjectInterface::STATUS_ACCEPTED);

        $this->retrieveProject->expects($this->once())->method('execute')->willReturn(true);

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

        $this->retrieveProject->expects($this->never())->method('execute');

        $result = $this->sut->executeById($projectId);

        $this->assertFalse($result);
    }

}
