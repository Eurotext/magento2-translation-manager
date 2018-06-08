<?php
/**
 * @copyright see LICENSE.txt
 *
 * @see LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration;

use Eurotext\TranslationManager\Seeder\ProductSeeder;
use Eurotext\TranslationManager\Test\Integration\Provider\ProjectProvider;

class ProductSeederIntegrationTest extends IntegrationTestAbstract
{
    /** @var ProductSeeder */
    protected $sut;

    /** @var ProjectProvider */
    private $projectProvider;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = $this->objectManager->create(ProductSeeder::class);

        $this->projectProvider = $this->objectManager->get(ProjectProvider::class);
    }

    /**
     * @magentoDataFixture loadFixture
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testItShouldSeedProjectProducts()
    {
        $name = __CLASS__ . '-product-seeder';

        $project = $this->projectProvider->createProject($name);

        $result = $this->sut->seed($project);

        $this->assertTrue($result);
    }

    public static function loadFixture()
    {
        include __DIR__ . '/../_fixtures/provide_products.php';
    }
}