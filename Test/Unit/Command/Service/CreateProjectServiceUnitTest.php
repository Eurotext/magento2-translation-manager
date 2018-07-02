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
use Eurotext\TranslationManager\Command\Service\CreateProjectService;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Eurotext\TranslationManager\Seeder\EntitySeederPool;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Builder\ProjectMockBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\TestCase;

class CreateProjectServiceUnitTest extends TestCase
{
    /** @var ObjectManagerHelper */
    protected $objectManager;

    /** @var ProjectFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectFactory;

    /** @var CreateProjectService */
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

        $this->objectManager = new ObjectManagerHelper($this);

        $this->projectFactory    = $this->projectMockBuilder->buildProjectFactoryMock();
        $this->projectRepository = $this->projectMockBuilder->buildProjectRepositoryMock();
        $this->seederMock        = $this->projectMockBuilder->buildProjectSeederMock();

        $this->projectSeederPool = new EntitySeederPool([$this->seederMock]);

        $this->sut = $this->objectManager->getObject(
            CreateProjectService::class,
            [
                'projectFactory'    => $this->projectFactory,
                'projectRepository' => $this->projectRepository,
                'projectSeederPool' => $this->projectSeederPool,
            ]
        );
    }

    /**
     * @test
     */
    public function itShouldCreateANewProject()
    {
        $name      = 'my first project with a name';
        $storeSrc  = 1;
        $storeDest = 2;

        $project = $this->objectManager->getObject(Project::class);

        $this->projectFactory->expects($this->once())->method('create')->willReturn($project);

        $this->projectRepository->expects($this->once())->method('save')->willReturn($project);

        $this->seederMock->expects($this->once())->method('seed')->willReturn(true);

        $input = $this->consoleMockBuilder->buildConsoleInputMock();
        $input->expects($this->any())
              ->method('getArgument')
              ->willReturnOnConsecutiveCalls($name, $storeSrc, $storeDest);

        $output = $this->consoleMockBuilder->buildConsoleOutputMock();

        $this->sut->execute($input, $output);

    }

}
