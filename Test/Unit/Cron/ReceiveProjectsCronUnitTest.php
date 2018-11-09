<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Cron;

use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Cron\ReceiveProjectsCron;
use Eurotext\TranslationManager\Service\ReceiveProjectServiceInterface;
use Eurotext\TranslationManager\Test\Builder\ProjectMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResults;
use Psr\Log\LoggerInterface;

class ReceiveProjectsCronUnitTest extends UnitTestAbstract
{
    /** @var ReceiveProjectsCron */
    private $sut;

    /** @var ReceiveProjectServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $receiveProjectService;

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

        $this->receiveProjectService =
            $this->getMockBuilder(ReceiveProjectServiceInterface::class)
                 ->setMethods(['execute'])
                 ->getMockForAbstractClass();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $this->sut = $this->objectManager->getObject(
            ReceiveProjectsCron::class,
            [
                'projectRepository'     => $this->projectRepository,
                'criteriaBuilder'       => $this->criteriaBuilder,
                'receiveProjectService' => $this->receiveProjectService,
                'logger'                => $this->logger,
            ]
        );
    }

    public function testItShouldReceiveAllProjectsInStatusAccepted()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->criteriaBuilder->expects($this->once())->method('create')->willReturn(new SearchCriteria());
        $this->projectRepository->expects($this->once())->method('getList')->willReturn($searchResults);

        $this->receiveProjectService->expects($this->once())->method('execute')->with($project)->willReturn(true);

        $this->logger->expects($this->never())->method('error');

        $this->sut->execute();
    }

    public function testItShouldLogReceiveExceptionOnResultFalse()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->criteriaBuilder->expects($this->once())->method('create')->willReturn(new SearchCriteria());
        $this->projectRepository->expects($this->once())->method('getList')->willReturn($searchResults);

        $this->receiveProjectService->expects($this->once())->method('execute')->with($project)->willReturn(false);

        $this->logger->expects($this->once())->method('error');

        $this->sut->execute();
    }

    public function testItShouldLogReceiveExceptions()
    {
        $project = $this->projectMockBuilder->buildProjectMock();

        $items         = [$project];
        $searchResults = new SearchResults();
        $searchResults->setItems($items);

        $this->criteriaBuilder->expects($this->once())->method('create')->willReturn(new SearchCriteria());
        $this->projectRepository->expects($this->once())->method('getList')->willReturn($searchResults);

        $this->receiveProjectService
            ->expects($this->once())->method('execute')
            ->with($project)->willThrowException(new \Exception());

        $this->logger->expects($this->once())->method('error');

        $this->sut->execute();
    }

}