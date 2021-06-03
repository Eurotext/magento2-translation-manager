<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service\Project;

use Eurotext\RestApiClient\Validator\ProjectStatusValidator;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\Service\Project\CheckProjectStatusService;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Builder\ProjectMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\Api\SearchResults;
use Psr\Log\LoggerInterface;

class CheckProjectStatusUnitTest extends UnitTestAbstract
{
    /** @var CheckProjectStatusService */
    private $sut;

    /** @var ProjectMockBuilder */
    private $projectMockBuilder;

    /** @var ProjectStatusValidator|\PHPUnit_Framework_MockObject_MockObject */
    private $projectStatusValidator;

    /** @var ProjectStateMachine|\PHPUnit_Framework_MockObject_MockObject */
    private $projectStateMachine;

    /** @var ProjectRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectMockBuilder = new ProjectMockBuilder($this);

        $this->projectRepository      = $this->createMock(ProjectRepositoryInterface::class);
        $this->projectStatusValidator = $this->createMock(ProjectStatusValidator::class);
        $this->projectStateMachine    = $this->createMock(ProjectStateMachine::class);
        $this->logger                 = $this->createMock(LoggerInterface::class);

        $this->sut = $this->objectManager->getObject(
            CheckProjectStatusService::class,
            [
                'projectRepository'      => $this->projectRepository,
                'projectStatusValidator' => $this->projectStatusValidator,
                'projectStateMachine'    => $this->projectStateMachine,
                'logger'                 => $this->logger,
            ]
        );
    }

    public function testItShouldUpdateProjectStatus()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(true);

        $this->projectStateMachine->expects($this->once())
                                  ->method('apply')
                                  ->with($project, ProjectInterface::STATUS_TRANSLATED);

        $this->sut->execute($project);
    }

    public function testItShouldLoadProjectViaRepository()
    {
        $projectId = 1;
        $project   = $this->projectMockBuilder->buildProjectMock();

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(true);

        $this->projectStateMachine->expects($this->once())
                                  ->method('apply')
                                  ->with($project, ProjectInterface::STATUS_TRANSLATED);

        $this->sut->executeById($projectId);
    }

    public function testItShouldSkipProjectsNotYetFinished()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(false);

        $this->projectStateMachine->expects($this->never())->method('apply');

        $this->sut->execute($project);
    }

    /**
     * @dataProvider dataProviderStateMachineExceptions
     */
    public function testItShouldCatchExceptionsDuringStatusApply($exceptionclass)
    {
        $exception = new $exceptionclass();

        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(true);

        $this->projectStateMachine->expects($this->once())
                                  ->method('apply')
                                  ->willThrowException($exception);

        $this->logger->expects($this->once())->method('error');

        $this->sut->execute($project);
    }

    public function dataProviderStateMachineExceptions()
    {
        return [
            [IllegalProjectStatusChangeException::class],
            [InvalidProjectStatusException::class],
        ];

    }
}
