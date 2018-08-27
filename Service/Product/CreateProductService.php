<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service\Product;

use Eurotext\RestApiClient\Api\Project\ItemV1ApiInterface;
use Eurotext\RestApiClient\Request\Data\Project\ItemData;
use Eurotext\RestApiClient\Request\Project\ItemDataRequest;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Entity\ProductEntityType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CreateProductService
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ItemV1ApiInterface
     */
    private $itemV1Api;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ItemV1ApiInterface $itemV1Api
    ) {
        $this->productRepository = $productRepository;
        $this->itemV1Api         = $itemV1Api;
    }

    /**
     * @param ProjectInterface $project
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function executeById(ProjectInterface $project, int $id): bool
    {
        $product = $this->productRepository->getById($id);

        return $this->execute($project, $product);
    }

    public function execute(ProjectInterface $project, ProductInterface $product): bool
    {
        $extId = $project->getExtId();

        // ItemData
        $originalString = '';

        $meta = [
            'product_id'   => $product->getId(),
            'item_id'   => $product->getId(), // @todo ProjectProduct-Id
        ];

        $itemData = new ItemData($originalString, $meta);

        // ItemDataRequest
        $source       = 'en-us'; // @todo get from Project
        $target       = 'de-de'; // @todo get from Project
        $textType     = ProductEntityType::CODE;
        $systemModule = 'Magento'; // @todo where do we get this from? does this need to be defined here?

        $itemDataRequest = new ItemDataRequest($extId, $source, $target, $textType, $systemModule, $itemData);

        // Send Request
        $this->itemV1Api->post($itemDataRequest);

        return true;
    }
}