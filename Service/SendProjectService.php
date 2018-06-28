<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntitySenderInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Sender\EntitySenderPool;

class SendProjectService
{
    /**
     * @var \Eurotext\TranslationManager\Api\ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var \Eurotext\TranslationManager\Sender\EntitySenderPool
     */
    private $entitySenderPool;

    public function __construct(ProjectRepositoryInterface $projectRepository, EntitySenderPool $entitySenderPool)
    {
        $this->projectRepository = $projectRepository;
        $this->entitySenderPool = $entitySenderPool;
    }

    public function executeById(int $id)
    {
        $project = $this->projectRepository->getById($id);

        return $this->execute($project);
    }

    public function execute(ProjectInterface $project)
    {
        // @todo create project via ApiClient
        // @todo save project ext_id

        // @todo create project items dynamically by internal project entities
        $senders = $this->entitySenderPool->getItems();

        foreach ($senders as $sender) {
            /** @var $sender EntitySenderInterface */
            $sender->send($project);
        }
    }
}
