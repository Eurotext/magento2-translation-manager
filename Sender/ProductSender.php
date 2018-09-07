<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Sender;

use Eurotext\RestApiClient\Api\Project\ItemV1ApiInterface;
use Eurotext\RestApiClient\Request\Data\Project\ItemData;
use Eurotext\RestApiClient\Request\Project\ItemDataRequest;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\Data\ProjectProductInterface;
use Eurotext\TranslationManager\Api\EntitySenderInterface;
use Eurotext\TranslationManager\Api\ProjectProductRepositoryInterface;
use Eurotext\TranslationManager\Setup\EntitySchema\ProjectProductSchema;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class ProductSender implements EntitySenderInterface
{
    /**
     * @var ProjectProductRepositoryInterface
     */
    private $projectProductRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ItemV1ApiInterface
     */
    private $itemApi;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    const CONFIG_PATH_LOCALE_CODE = 'general/locale/code';

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ItemV1ApiInterface $itemApi,
        ProjectProductRepositoryInterface $projectProductRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->projectProductRepository = $projectProductRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->itemApi                  = $itemApi;
        $this->productRepository        = $productRepository;
        $this->scopeConfig              = $scopeConfig;
        $this->logger                   = $logger;
    }

    public function send(ProjectInterface $project): bool
    {
        $projectId     = $project->getId();
        $scopeCodeSrc  = $project->getStoreviewSrc();
        $scopeCodeDest = $project->getStoreviewDst();

        $languageSrc  = $this->scopeConfig->getValue(self::CONFIG_PATH_LOCALE_CODE, 'stores', $scopeCodeSrc);
        $languageDest = $this->scopeConfig->getValue(self::CONFIG_PATH_LOCALE_CODE, 'stores', $scopeCodeDest);

        $this->logger->info(sprintf('send project products project-id:%d', $projectId));

        $this->searchCriteriaBuilder->addFilter(ProjectProductSchema::PROJECT_ID, $projectId);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $searchResult = $this->projectProductRepository->getList($searchCriteria);

        $projectProducts = $searchResult->getItems();

        foreach ($projectProducts as $projectProduct) {
            /** @var $projectProduct ProjectProductInterface */
            $productId = $projectProduct->getProductId();

            $product = $this->productRepository->getById($productId);

            $data = [
                'name' => $product->getName(),
                // @todo get attributes to map
            ];
            $meta = [
                'item_id' => $product->getId(),
                'entity_id' => $product->getId(),
            ];

            $itemData = new ItemData($data, $meta);

            $itemRequest = new ItemDataRequest(
                $projectId, $languageSrc, $languageDest, 'product', '', $itemData
            );

            try {
                $response = $this->itemApi->post($itemRequest);

                // save project_product ext_id
                $extId = $response->getId();
                $projectProduct->setExtId($extId);

                $this->projectProductRepository->save($projectProduct);

                $this->logger->info(sprintf('product id:%d, ext-id:%s => success', $productId, $extId));
            } catch (GuzzleException $e) {
                $message = $e->getMessage();
                $this->logger->error(sprintf('product id:%d => %s', $productId, $message));
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $this->logger->error(sprintf('product id:%d => %s', $productId, $message));
            }

        }

        return true;
    }
}
