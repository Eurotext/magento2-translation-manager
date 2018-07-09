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

        foreach ($senders as $sender) {
            $senderClass = \get_class($sender);

            try {
                $sender->send($project);

                $result[$senderClass] = 1;
                $this->logger->info(sprintf('%s => success', $senderClass));
            } catch (\Exception $e) {
                $message = $e->getMessage();

                $result[$senderClass] = $message;
                $this->logger->error(sprintf('%s => %s', $senderClass, $message));
            }
        }

        return $result;
    }
}