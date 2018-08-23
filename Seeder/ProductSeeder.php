<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Seeder;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\Data\ProjectProductInterface;
use Eurotext\TranslationManager\Api\EntitySeederInterface;
use Eurotext\TranslationManager\Api\ProjectProductRepositoryInterface;
use Eurotext\TranslationManager\Model\ProjectProductFactory;
use Eurotext\TranslationManager\Setup\EntitySchema\ProjectProductSchema;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaInterfaceFactory;

/**
 * ProductSeeder
 */
class ProductSeeder implements EntitySeederInterface
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

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaInterfaceFactory $searchCriteriaFactory,
        ProjectProductFactory $projectProductFactory,
        ProjectProductRepositoryInterface $projectProductRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productRepository        = $productRepository;
        $this->searchCriteriaFactory    = $searchCriteriaFactory;
        $this->projectProductFactory    = $projectProductFactory;
        $this->projectProductRepository = $projectProductRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
    }

    public function seed(ProjectInterface $project): bool
    {
        $result = true;

        // get product collection
        /** @var $searchCriteria SearchCriteriaInterface */
        $searchCriteria = $this->searchCriteriaFactory->create();
        $searchResult   = $this->productRepository->getList($searchCriteria);

        // create project product configurations
        $products = $searchResult->getItems();

        $projectId = $project->getId();
        foreach ($products as $product) {
            /** @var $product ProductInterface */
            $productId = (int)$product->getId();

            $this->searchCriteriaBuilder
                ->addFilter(ProjectProductSchema::PRODUCT_ID, $productId)
                ->addFilter(ProjectProductSchema::PROJECT_ID, $projectId);
            $searchCriteria = $this->searchCriteriaBuilder->create();

            $searchResults = $this->projectProductRepository->getList($searchCriteria);

            if ($searchResults->getTotalCount() >= 1) {
                continue;
            }

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
