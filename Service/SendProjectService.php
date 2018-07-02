<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service;

use Eurotext\RestApiClient\Api\ProjectV1Api;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Mapper\ProjectPostMapper;
use Eurotext\TranslationManager\Sender\EntitySenderPool;
use Psr\Log\LoggerInterface;

class SendProjectService
{
    /** @var ProjectPostMapper */
    private $projectPostMapper;

    /**
     * @var \Eurotext\TranslationManager\Api\ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var \Eurotext\TranslationManager\Sender\EntitySenderPool
     */
    private $entitySenderPool;

    /**
     * @var ProjectV1Api
     */
    private $projectApi;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectPostMapper $projectPostMapper,
        ProjectV1Api $projectApi,
        EntitySenderPool $entitySenderPool,
        LoggerInterface $logger
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectApi        = $projectApi;
        $this->entitySenderPool  = $entitySenderPool;
        $this->projectPostMapper = $projectPostMapper;
        $this->logger            = $logger;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \Eurotext\RestApiClient\Exception\ProjectApiException
     */
    public function executeById(int $id): array
    {
        $project = $this->projectRepository->getById($id);

        return $this->execute($project);
    }

    /**
     * @param ProjectInterface $project
     *
     * @return array
     *
     * @throws \Eurotext\RestApiClient\Exception\ProjectApiException
     */
    public function execute(ProjectInterface $project): array
    {
        $result = [];

        if ($project->getExtId() === 0) {
            // create project via ApiClient
            $id = $project->getId();
            $this->logger->info(sprintf('send project post for id:%d', $id));

            $request  = $this->projectPostMapper->map($project);
            $response = $this->projectApi->post($request);

            // save project ext_id
            $extId = $response->getId();
            $project->setExtId($extId);

            $this->logger->info(sprintf('save project id:%d ext-id:%d', $id, $extId));
            $this->projectRepository->save($project);
        }

        // create project items dynamically by internal project entities
        $senders = $this->entitySenderPool->getItems();

        foreach ($senders as $sender) {
            $senderClass = \get_class($sender);

            try {
                $sender->send($project);

                $result[$senderClass] = 1;
                $this->logger->info(sprintf('%s => success', $sender));
            } catch (\Exception $e) {
                $message = $e->getMessage();

                $result[$senderClass] = $message;

                $this->logger->error(sprintf('%s => %s', $sender, $message));
            }
        }

        return $result;
    }
}
