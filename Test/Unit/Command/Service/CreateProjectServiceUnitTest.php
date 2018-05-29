<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command\Service;

use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Api\ProjectSeederInterface;
use Eurotext\TranslationManager\Command\Service\CreateProjectService;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Eurotext\TranslationManager\Seeder\ProjectSeederPool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProjectServiceUnitTest extends TestCase
{
    /** @var ObjectManagerHelper */
    protected $objectManager;

    /** @var ProjectFactory|\PHPUnit\Framework\MockObject\MockObject */
    protected $projectFactory;

    /** @var CreateProjectService */
    protected $sut;

    /** @var ProjectRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $projectRepository;

    /** @var ProjectSeederInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $seederMock;

    /** @var ProjectSeederPool */
    protected $projectSeederPool;

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = new ObjectManagerHelper($this);

        $this->projectFactory = $this->buildProjectFactoryMock();
        $this->projectRepository = $this->buildProjectRepositoryMock();
        $this->seederMock = $this->buildProjectSeederMock();

        $this->projectSeederPool = new ProjectSeederPool([$this->seederMock]);

        $this->sut = $this->objectManager->getObject(
            CreateProjectService::class,
            [
                'projectFactory' => $this->projectFactory,
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
        $name = 'my first project with a name';
        $storeSrc = 1;
        $storeDest = 2;

        $project = $this->objectManager->getObject(Project::class);

        $this->projectFactory->expects($this->once())->method('create')->willReturn($project);

        $this->projectRepository->expects($this->once())->method('save')->willReturn($project);

        $this->seederMock->expects($this->once())->method('seed')->willReturn(true);

        $input = $this->buildConsoleInputMock();
        $input->expects($this->any())->method('getArgument')
            ->willReturnOnConsecutiveCalls($name, $storeSrc, $storeDest);

        $output = $this->buildConsoleOutputMock();

        $this->sut->execute($input, $output);

    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function buildConsoleOutputMock()
    {
        /** @var OutputInterface|\PHPUnit\Framework\MockObject\MockObject $output */
        $output = $this->getMockBuilder(OutputInterface::class)
            ->setMethods(['writeln'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        return $output;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|InputInterface
     */
    protected function buildConsoleInputMock()
    {
        $input = $this->getMockBuilder(InputInterface::class)
            ->setMethods(['getArgument'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        return $input;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildProjectSeederMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ProjectSeederInterface::class)
            ->setMethods(['seed'])
            ->getMockForAbstractClass();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildProjectRepositoryMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ProjectRepositoryInterface::class)
            ->setMethods(['save'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildProjectFactoryMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ProjectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
    }
}