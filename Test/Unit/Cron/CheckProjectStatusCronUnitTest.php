<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Cron;

use Eurotext\RestApiClient\Validator\ProjectStatusValidator;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Cron\CheckProjectStatusCron;
use Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Builder\ProjectMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResults;
use Psr\Log\LoggerInterface;

class CheckProjectStatusCronUnitTest extends UnitTestAbstract
{
    /** @var CheckProjectStatusCron */
    private $sut;

    /** @var ProjectMockBuilder */
    private $projectMockBuilder;

    /** @var ProjectRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    /** @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject */
    private $criteriaBuilder;

    /** @var ProjectStatusValidator|\PHPUnit_Framework_MockObject_MockObject */
    private $projectStatusValidator;

    /** @var ProjectStateMachine|\PHPUnit_Framework_MockObject_MockObject */
    private $projectStateMachine;

    /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    protected function setUp()
    {
        parent::setUp();

        $this->projectMockBuilder = new ProjectMockBuilder($this);

        $this->criteriaBuilder =
            $this->getMockBuilder(SearchCriteriaBuilder::class)
                 ->setMethods(['create', 'addFilter'])
                 ->disableOriginalConstructor()
                 ->getMock();

        $this->projectRepository = $this->getMockBuilder(ProjectRepositoryInterface::class)->getMock();

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

        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $this->sut = $this->objectManager->getObject(
            CheckProjectStatusCron::class,
            [
                'projectRepository'      => $this->projectRepository,
                'criteriaBuilder'        => $this->criteriaBuilder,
                'projectStatusValidator' => $this->projectStatusValidator,
                'projectStateMachine'    => $this->projectStateMachine,
                'logger'                 => $this->logger,
            ]
        );
    }

    public function testItShouldUpdateProjectStatus()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->criteriaBuilder->expects($this->once())->method('create')->willReturn(new SearchCriteria());
        $this->projectRepository->expects($this->once())->method('getList')->willReturn($searchResults);

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(true);

        $this->projectStateMachine->expects($this->once())
                                  ->method('apply')
                                  ->with($project, ProjectInterface::STATUS_TRANSLATED);

        $this->sut->execute();
    }

    public function testItShouldSkipProjectsNotYetFinished()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->criteriaBuilder->expects($this->once())->method('create')->willReturn(new SearchCriteria());
        $this->projectRepository->expects($this->once())->method('getList')->willReturn($searchResults);

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(false);

        $this->projectStateMachine->expects($this->never())->method('apply');

        $this->sut->execute();
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

        $this->criteriaBuilder->expects($this->once())->method('create')->willReturn(new SearchCriteria());
        $this->projectRepository->expects($this->once())->method('getList')->willReturn($searchResults);

        $this->projectStatusValidator->expects($this->once())->method('validate')->willReturn(true);

        $this->projectStateMachine->expects($this->once())
                                  ->method('apply')
                                  ->willThrowException($exception);

        $this->logger->expects($this->once())->method('error');

        $this->sut->execute();
    }

    public function dataProviderStateMachineExceptions()
    {
        return [
            [IllegalProjectStatusChangeException::class],
            [InvalidProjectStatusException::class],
        ];

    }
}