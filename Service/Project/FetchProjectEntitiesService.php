<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service\Project;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Entity\EntityRetrieverPool;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class FetchProjectEntitiesService implements FetchProjectEntitiesServiceInterface
{
    /**
     * @var EntityRetrieverPool
     */
    private $entityRetrieverPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        EntityRetrieverPool $entityRetrieverPool,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->entityRetrieverPool = $entityRetrieverPool;
        $this->storeManager       = $storeManager;
        $this->logger             = $logger;
    }

    public function execute(ProjectInterface $project): array
    {
        $result = [];

        // Initialize Destination Store for this project
        // This needs to be done because the ProductRepository sets the store_id to the storeManager->getCurrentStore
        $store = $this->storeManager->getStore($project->getStoreviewDst());
        $this->storeManager->setCurrentStore($store->getId());

        // retrieve project items dynamically by internal project entities
        $retrievers = $this->entityRetrieverPool->getItems();

        foreach ($retrievers as $retrieverKey => $retriever) {
            try {

                $retriever->retrieve($project);

                $result[$retrieverKey] = true;
                $this->logger->info(sprintf('%s => success', $retrieverKey));
            } catch (\Exception $e) {
                $message = $e->getMessage();

                $result[$retrieverKey] = $message;
                $this->logger->error(sprintf('%s => %s', $retrieverKey, $message));
            }
        }

        return $result;
    }
}