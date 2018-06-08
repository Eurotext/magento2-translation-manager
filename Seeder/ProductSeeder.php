<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Seeder;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\Data\ProjectProductInterface;
use Eurotext\TranslationManager\Api\ProjectProductRepositoryInterface;
use Eurotext\TranslationManager\Api\ProjectSeederInterface;
use Eurotext\TranslationManager\Model\ProjectProductFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaInterfaceFactory;

/**
 * ProductSeeder
 */
class ProductSeeder implements ProjectSeederInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaInterfaceFactory
     */
    private $searchCriteriaFactory;

    /**
     * @var ProjectProductFactory
     */
    private $projectProductFactory;

    /**
     * @var ProjectProductRepositoryInterface
     */
    private $projectProductRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaInterfaceFactory $searchCriteriaFactory,
        ProjectProductFactory $projectProductFactory,
        ProjectProductRepositoryInterface $projectProductRepository
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->projectProductFactory = $projectProductFactory;
        $this->projectProductRepository = $projectProductRepository;
    }

    public function seed(ProjectInterface $project): bool
    {
        $result = true;

        // get product collection
        /** @var $searchCriteria SearchCriteriaInterface */
        $searchCriteria = $this->searchCriteriaFactory->create();
        $searchResult = $this->productRepository->getList($searchCriteria);

        // create project product configurations
        $products = $searchResult->getItems();

        $projectId = $project->getId();
        foreach ($products as $product) {
            /** @var $product ProductInterface */
            $productId = $product->getId();

            /** @var ProjectProductInterface $projectProduct */
            $projectProduct = $this->projectProductFactory->create();
            $projectProduct->setProjectId($projectId);
            $projectProduct->setProductId($productId);

            try {
                $this->projectProductRepository->save($projectProduct);
            } catch (\Exception $e) {
                $result = false;
            }
        }

        return $result;
    }
}