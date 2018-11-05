<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Cron;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Cron\CheckProjectStatusCron;
use Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\Service\Project\CheckProjectStatusServiceInterface;
use Eurotext\TranslationManager\Test\Builder\ProjectMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResults;

class CheckProjectStatusCronUnitTest extends UnitTestAbstract
{
    /** @var CheckProjectStatusCron */
    private $sut;

    /** @var CheckProjectStatusServiceInterface */
    private $checkProjectStatus;

    /** @var ProjectMockBuilder */
    private $projectMockBuilder;

    /** @var ProjectRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    /** @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject */
    private $criteriaBuilder;

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

        $this->checkProjectStatus = $this->getMockBuilder(CheckProjectStatusServiceInterface::class)->getMock();

        $this->sut = $this->objectManager->getObject(
            CheckProjectStatusCron::class,
            [
                'projectRepository'  => $this->projectRepository,
                'criteriaBuilder'    => $this->criteriaBuilder,
                'checkProjectStatus' => $this->checkProjectStatus,
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

        $this->checkProjectStatus->expects($this->once())->method('execute')->willReturn(true);

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

        $this->checkProjectStatus->expects($this->once())->method('execute')->willReturn(false);

        $this->sut->execute();
    }

}