<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command\Service;

use Eurotext\TranslationManager\Api\EntitySeederInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Command\Service\NewProjectService;
use Eurotext\TranslationManager\Command\Service\SeedEntitiesService;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Seeder\EntitySeederPool;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Builder\ProjectMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class SeedEntitiesCliServiceUnitTest extends UnitTestAbstract
{
    /** @var NewProjectService */
    protected $sut;

    /** @var ProjectRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectRepository;

    /** @var EntitySeederInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $seederMock;

    /** @var EntitySeederPool */
    protected $projectSeederPool;

    /** @var ProjectMockBuilder */
    protected $projectMockBuilder;

    /** @var ConsoleMockBuilder */
    protected $consoleMockBuilder;

    protected function setUp()
    {
        parent::setUp();

        $this->projectMockBuilder = new ProjectMockBuilder($this);
        $this->consoleMockBuilder = new ConsoleMockBuilder($this);

        $this->projectRepository = $this->projectMockBuilder->buildProjectRepositoryMock();

    }

    public function testItShouldCallAllMatchingSeeders()
    {
        $projectId   = 1;
        $entityCode  = 'entity-code';
        $failingCode = 'failing';
        $entities    = "$entityCode,$failingCode,unknown-code";

        // ProjectRepository
        $project = $this->objectManager->getObject(Project::class);
        $this->projectRepository->expects($this->once())->method('getById')->willReturn($project);

        // Prepare Seeder & SeederPool
        $seeder = $this->getMockBuilder(EntitySeederInterface::class)->getMock();
        $seeder->expects($this->once())->method('seed')->with($project)->willReturn(true);

        $seederFailing = $this->getMockBuilder(EntitySeederInterface::class)->getMock();
        $seederFailing->expects($this->once())->method('seed')->with($project)->willReturn(false);

        $this->projectSeederPool = new EntitySeederPool([$entityCode => $seeder, $failingCode => $seederFailing]);

        // Prepare SUT
        $this->sut = $this->objectManager->getObject(
            SeedEntitiesService::class,
            [
                'entitySeederPool'  => $this->projectSeederPool,
                'projectRepository' => $this->projectRepository,
            ]
        );

        // Input & Output
        $input = $this->consoleMockBuilder->buildConsoleInputMock();
        $input->method('getArgument')
              ->willReturnOnConsecutiveCalls($projectId, $entities);

        $output = $this->consoleMockBuilder->buildConsoleOutputMock();

        // Execute Test
        $this->sut->execute($input, $output);
    }

}
