<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Provider;

use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectProduct;
use Eurotext\TranslationManager\Repository\ProjectProductRepository;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Magento\TestFramework\Helper\Bootstrap;

class ProjectProvider
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $objectManager;

    /** @var ProjectRepository */
    private $projectRepository;

    /** @var $projectProductRepository ProjectProductRepository */
    private $projectProductRepository;

    public function __construct()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->projectRepository = $this->objectManager->get(ProjectRepository::class);
        $this->projectProductRepository = $this->objectManager->get(ProjectProductRepository::class);
    }

    /**
     * @param $name
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectInterface|Project
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createProject($name)
    {
        /** @var Project $project */
        $project = $this->objectManager->get(Project::class);
        $project->setName($name);

        return $this->projectRepository->save($project);
    }

    /**
     * @param int $projectId
     * @param int $productId
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectProductInterface|\Eurotext\TranslationManager\Model\ProjectProduct
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createProjectProduct(int $projectId, int $productId)
    {
        /** @var ProjectProduct $object */
        $object = $this->objectManager->create(ProjectProduct::class);
        $object->setProjectId($projectId);
        $object->setProductId($productId);

        return $this->projectProductRepository->save($object);
    }
}