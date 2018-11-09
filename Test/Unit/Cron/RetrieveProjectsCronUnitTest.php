<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Cron;

use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Cron\RetrieveProjectsCron;
use Eurotext\TranslationManager\Service\RetrieveProjectServiceInterface;
use Eurotext\TranslationManager\Test\Builder\ProjectMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResults;
use Psr\Log\LoggerInterface;

class RetrieveProjectsCronUnitTest extends UnitTestAbstract
{
    /** @var RetrieveProjectsCron */
    private $sut;

    /** @var RetrieveProjectServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $retrieveProjectService;

    /** @var ProjectMockBuilder */
    private $projectMockBuilder;

    /** @var ProjectRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    /** @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject */
    private $criteriaBuilder;

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

        $this->projectRepository =
            $this->getMockBuilder(ProjectRepositoryInterface::class)
                 ->getMockForAbstractClass();

        $this->retrieveProjectService =
            $this->getMockBuilder(RetrieveProjectServiceInterface::class)
                 ->setMethods(['execute'])
                 ->getMockForAbstractClass();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $this->sut = $this->objectManager->getObject(
            RetrieveProjectsCron::class,
            [
                'projectRepository'     => $this->projectRepository,
                'criteriaBuilder'       => $this->criteriaBuilder,
                'retrieveProjectService' => $this->retrieveProjectService,
                'logger'                => $this->logger,
            ]
        );
    }

    public function testItShouldRetrieveAllProjectsInStatusAccepted()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->criteriaBuilder->expects($this->once())->method('create')->willReturn(new SearchCriteria());
        $this->projectRepository->expects($this->once())->method('getList')->willReturn($searchResults);

        $this->retrieveProjectService->expects($this->once())->method('execute')->with($project)->willReturn(true);

        $this->logger->expects($this->never())->method('error');

        $this->sut->execute();
    }

    public function testItShouldLogRetrieveExceptionOnResultFalse()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->criteriaBuilder->expects($this->once())->method('create')->willReturn(new SearchCriteria());
        $this->projectRepository->expects($this->once())->method('getList')->willReturn($searchResults);

        $this->retrieveProjectService->expects($this->once())->method('execute')->with($project)->willReturn(false);

        $this->logger->expects($this->once())->method('error');

        $this->sut->execute();
    }

    public function testItShouldLogRetrieveExceptions()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->criteriaBuilder->expects($this->once())->method('create')->willReturn(new SearchCriteria());
        $this->projectRepository->expects($this->once())->method('getList')->willReturn($searchResults);

        $this->retrieveProjectService
            ->expects($this->once())->method('execute')
            ->with($project)->willThrowException(new \Exception());

        $this->logger->expects($this->once())->method('error');

        $this->sut->execute();
    }

}