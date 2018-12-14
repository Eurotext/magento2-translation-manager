<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Console\Service;

use Eurotext\TranslationManager\Api\EntitySeederInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Console\Service\NewProjectService;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Eurotext\TranslationManager\Seeder\EntitySeederPool;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Builder\ProjectMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class NewProjectServiceUnitTest extends UnitTestAbstract
{
    /** @var ProjectFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectFactory;

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

    /** @var StoreRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->projectMockBuilder = new ProjectMockBuilder($this);
        $this->consoleMockBuilder = new ConsoleMockBuilder($this);

        $this->projectFactory    = $this->projectMockBuilder->buildProjectFactoryMock();
        $this->projectRepository = $this->projectMockBuilder->buildProjectRepositoryMock();
        $this->storeRepository   = $this->createMock(StoreRepositoryInterface::class);

        $this->sut = $this->objectManager->getObject(
            NewProjectService::class,
            [
                'projectFactory'    => $this->projectFactory,
                'projectRepository' => $this->projectRepository,
                'storeRepository'   => $this->storeRepository,
            ]
        );
    }

    /**
     * @test
     */
    public function itShouldCreateANewProject()
    {
        $name        = 'my first project with a name';
        $storeSrc    = 'source';
        $storeDest   = 'dest';
        $storeSrcId  = 1;
        $storeDestId = 3;

        $project = $this->objectManager->getObject(Project::class);

        $this->projectFactory->expects($this->once())->method('create')->willReturn($project);

        $this->projectRepository->expects($this->once())->method('save')->willReturn($project);

        $stockSrcObj = $this->createMock(StoreInterface::class);
        $stockSrcObj->expects($this->once())->method('getId')->willReturn($storeSrcId);

        $stockDestObj = $this->createMock(StoreInterface::class);
        $stockDestObj->expects($this->once())->method('getId')->willReturn($storeDestId);

        $this->storeRepository->expects($this->exactly(2))
                              ->method('get')
                              ->willReturnOnConsecutiveCalls($stockSrcObj, $stockDestObj);

        $input = $this->consoleMockBuilder->buildConsoleInputMock();
        $input->expects($this->any())
              ->method('getArgument')
              ->willReturnOnConsecutiveCalls($name, $storeSrc, $storeDest);

        $output = $this->consoleMockBuilder->buildConsoleOutputMock();

        $this->sut->execute($input, $output);

    }

}
