<?php
/**
 * @copyright see LICENSE.txt
 *
 * @see LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Repository;

use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectProduct;
use Eurotext\TranslationManager\Repository\ProjectProductRepository;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;
use Magento\Framework\Exception\NoSuchEntityException;

class ProjectProductRepositoryIntegrationTest extends IntegrationTestAbstract
{
    /** @var ProjectProductRepository */
    protected $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = $this->objectManager->get(ProjectProductRepository::class);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testItShouldCreateAProjectProductAndGetItById()
    {
        $productId = 1;
        $project = $this->createProject(__CLASS__ . '-test-getById');
        $projectId = $project->getId();

        $projectProduct = $this->createProjectProduct($projectId, $productId);

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
        $project = $this->createProject(__CLASS__ . '-test-delete');
        $projectId = $project->getId();

        $projectProduct = $this->createProjectProduct($projectId, $productId);

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

        $project = $this->createProject(__CLASS__ . '-test-list');
        $projectId = $project->getId();

        $projectProducts = [];
        foreach ($productIds as $productId) {
            $projectProduct = $this->createProjectProduct($projectId, $productId);

            $projectProducts[$productId] = $projectProduct;
        }

        /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->objectManager->get(\Magento\Framework\Api\SearchCriteria::class);

        $searchResults = $this->sut->getList($searchCriteria);

        $items = $searchResults->getItems();
        $this->assertTrue(count($items) >= count($projectProducts));
    }

    /**
     * @param $name
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectInterface|Project
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function createProject($name)
    {
        /** @var Project $project */
        $project = $this->objectManager->get(Project::class);
        $project->setName($name);

        /** @var ProjectRepository $projectRepository */
        $projectRepository = $this->objectManager->get(ProjectRepository::class);
        $project = $projectRepository->save($project);

        return $project;
    }

    /**
     * @param int $projectId
     * @param int $productId
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectProductInterface|\Eurotext\TranslationManager\Model\ProjectProduct
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function createProjectProduct(int $projectId, int $productId)
    {
        /** @var ProjectProduct $object */
        $object = $this->objectManager->create(ProjectProduct::class);
        $object->setProjectId($projectId);
        $object->setProductId($productId);

        $object = $this->sut->save($object);

        return $object;
    }
}