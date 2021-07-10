<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Command\Service;

use Eurotext\TranslationManager\Console\Service\NewProjectService;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;
use Eurotext\TranslationManager\Test\Integration\Provider\StoreProvider;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

class NewProjectServiceIntegrationTest extends IntegrationTestAbstract
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $objectManager;

    /** @var NewProjectService */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->objectManager = Bootstrap::getObjectManager();

        $this->sut = $this->objectManager->get(NewProjectService::class);
    }

    /**
     * @magentoDataFixture loadFixtures
     * @test
     */
    public function itShouldCreateANewProject()
    {
        $name          = 'my first project with a name';
        $storeSrcId    = 1;
        $storeSrcCode  = 'default';
        $storeDestCode = 'store_dest';

        /** @var Store $storeDest */
        $storeDest = $this->objectManager->create(Store::class);
        $storeDest->load($storeDestCode, 'code');

        $storeDestId = $storeDest->getStoreId();

        $input = $this->builder->buildConsoleInputMock();
        $input->expects($this->exactly(3))
              ->method('getArgument')->willReturnOnConsecutiveCalls($name, $storeSrcCode, $storeDestCode);

        $output = new \Symfony\Component\Console\Tests\Fixtures\DummyOutput();

        $project = $this->sut->execute($input, $output);

        $this->assertNotEmpty($project->getId());
        $this->assertNotEmpty($project->getCode());
        $this->assertEquals($storeSrcId, $project->getStoreviewSrc());
        $this->assertEquals($storeDestId, $project->getStoreviewDst());
    }

    public static function loadFixtures()
    {
        StoreProvider::createStore('store_dest');
    }
}
