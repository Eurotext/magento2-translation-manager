<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service\Project;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Receiver\EntityReceiverPool;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class FetchProjectEntitiesService implements FetchProjectEntitiesServiceInterface
{
    /**
     * @var EntityReceiverPool
     */
    private $entityReceiverPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        EntityReceiverPool $entityReceiverPool,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->entityReceiverPool = $entityReceiverPool;
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

        // receive project items dynamically by internal project entities
        $receivers = $this->entityReceiverPool->getItems();

        foreach ($receivers as $receiverKey => $receiver) {
            try {

                $receiver->receive($project);

                $result[$receiverKey] = true;
                $this->logger->info(sprintf('%s => success', $receiverKey));
            } catch (\Exception $e) {
                $message = $e->getMessage();

                $result[$receiverKey] = $message;
                $this->logger->error(sprintf('%s => %s', $receiverKey, $message));
            }
        }

        return $result;
    }
}