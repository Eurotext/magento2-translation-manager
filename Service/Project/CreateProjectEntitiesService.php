<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service\Project;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Sender\EntitySenderPool;
use Psr\Log\LoggerInterface;

class CreateProjectEntitiesService
{
    /**
     * @var \Eurotext\TranslationManager\Sender\EntitySenderPool
     */
    private $entitySenderPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntitySenderPool $entitySenderPool,
        LoggerInterface $logger
    ) {
        $this->entitySenderPool = $entitySenderPool;
        $this->logger           = $logger;
    }

    public function execute(ProjectInterface $project): array
    {
        $result = [];

        // create project items dynamically by internal project entities
        $senders = $this->entitySenderPool->getItems();

        foreach ($senders as $senderKey => $sender) {
            try {
                $sender->send($project);

                $result[$senderKey] = 1;
                $this->logger->info(sprintf('%s => success', $senderKey));
            } catch (\Exception $e) {
                $message = $e->getMessage();

                $result[$senderKey] = $message;
                $this->logger->error(sprintf('%s => %s', $senderKey, $message));
            }
        }

        return $result;
    }
}