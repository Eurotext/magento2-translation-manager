<?php
/**
 * @copyright see LICENSE.txt
 *
 * @see LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Repository;

use Eurotext\TranslationManager\Repository\ProjectProductRepository;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;
use Eurotext\TranslationManager\Test\Integration\Provider\ProjectProvider;
use Magento\Framework\Exception\NoSuchEntityException;

class ProjectProductRepositoryIntegrationTest extends IntegrationTestAbstract
{
    /** @var ProjectProductRepository */
    protected $sut;

    /** @var ProjectProvider */
    private $projectProvider;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = $this->objectManager->get(ProjectProductRepository::class);

        $this->projectProvider = $this->objectManager->get(ProjectProvider::class);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testItShouldCreateAProjectProductAndGetItById()
    {
        $productId = 1;
        $name = __CLASS__ . '-test-getById';
        $project = $this->projectProvider->createProject($name);
        $projectId = $project->getId();

        $projectProduct = $this->projectProvider->createProjectProduct($projectId, $productId);

        $id = $projectProduct->getId();

        $this->assertTrue($id > 0);

        $projectRead = $this->sut->getById($id);

        $this->assertSame($id, $projectRead->getId());
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testItShouldDeleteProjectProducts()
    {
        $productId = 1;
        $name = __CLASS__ . '-test-delete';
        $project = $this->projectProvider->createProject($name);
        $projectId = $project->getId();

        $projectProduct = $this->projectProvider->createProjectProduct($projectId, $productId);

        $id = $projectProduct->getId();

        $result = $this->sut->deleteById($id);

        $this->assertTrue($result);

        try {
            $projectRead = $this->sut->getById($id);
        } catch (NoSuchEntityException $e) {
            $projectRead = null;
        }

        $this->assertNull($projectRead);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testItShouldReturnAListOfProjectProducts()
    {
        $productIds = [1, 2, 3];

        $name = __CLASS__ . '-test-list';
        $project = $this->projectProvider->createProject($name);
        $projectId = $project->getId();

        $projectProducts = [];
        foreach ($productIds as $productId) {
            $projectProduct = $this->projectProvider->createProjectProduct($projectId, $productId);

            $projectProducts[$productId] = $projectProduct;
        }

        /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->objectManager->get(\Magento\Framework\Api\SearchCriteria::class);

        $searchResults = $this->sut->getList($searchCriteria);

        $items = $searchResults->getItems();
        $this->assertTrue(count($items) >= count($projectProducts));
    }

}