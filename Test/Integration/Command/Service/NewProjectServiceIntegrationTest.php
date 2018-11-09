<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Command\Service;

use Eurotext\TranslationManager\Console\Command\Service\NewProjectService;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;
use Magento\TestFramework\Helper\Bootstrap;

class NewProjectServiceIntegrationTest extends IntegrationTestAbstract
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $objectManager;

    /** @var NewProjectService */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->objectManager = Bootstrap::getObjectManager();

        $this->sut = $this->objectManager->get(NewProjectService::class);
    }

    /**
     * @test
     */
    public function itShouldCreateANewProject()
    {
        $name      = 'my first project with a name';
        $storeSrc  = 1;
        $storeDest = 2;

        $input = $this->builder->buildConsoleInputMock();
        $input->expects($this->exactly(3))
              ->method('getArgument')->willReturnOnConsecutiveCalls($name, $storeSrc, $storeDest);

        $output = new \Symfony\Component\Console\Tests\Fixtures\DummyOutput();

        $project = $this->sut->execute($input, $output);

        $this->assertNotEmpty($project->getId());
        $this->assertNotEmpty($project->getCode());
        $this->assertEquals($storeSrc, $project->getStoreviewSrc());
        $this->assertEquals($storeDest, $project->getStoreviewDst());
    }

}
