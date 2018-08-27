<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Sender;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\Data\ProjectProductInterface;
use Eurotext\TranslationManager\Api\EntitySenderInterface;
use Eurotext\TranslationManager\Api\ProjectProductRepositoryInterface;
use Eurotext\TranslationManager\Setup\EntitySchema\ProjectProductSchema;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class ProductSender implements EntitySenderInterface
{
    /**
     * @var ProjectProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        ProjectProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productRepository     = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function send(ProjectInterface $project): bool
    {
        $projectId = $project->getId();

        $this->searchCriteriaBuilder->addFilter(ProjectProductSchema::PROJECT_ID, $projectId);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $searchResult = $this->productRepository->getList($searchCriteria);

        $products = $searchResult->getItems();

        foreach ($products as $product) {
            /** @var $product ProjectProductInterface */

        }

        return true;
    }
}
