<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Cron;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Service\SendProjectService;
use Eurotext\TranslationManager\Setup\EntitySchema\ProjectSchema;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;

class TransferProjectsCron
{

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var SendProjectService
     */
    private $sendProjectService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        SendProjectService $sendProjectService,
        LoggerInterface $logger
    ) {
        $this->projectRepository  = $projectRepository;
        $this->criteriaBuilder    = $criteriaBuilder;
        $this->sendProjectService = $sendProjectService;
        $this->logger             = $logger;
    }

    public function execute()
    {
        // Get all open project
        $this->criteriaBuilder->addFilter(ProjectSchema::STATUS, ProjectInterface::STATUS_TRANSFER);
        $searchCriteria = $this->criteriaBuilder->create();
        $searchResults  = $this->projectRepository->getList($searchCriteria);

        /** @var ProjectInterface[] $projects */
        $projects = $searchResults->getItems();

        // Fetch Status from Eurotext
        foreach ($projects as $project) {
            $projectId = $project->getId();
            try {
                $result = $this->sendProjectService->execute($project);
                if ($result === false) {
                    $msg = sprintf('project-id %d: error sending the project', $projectId);
                    $this->logger->error($msg);
                }
            } catch (\Exception $e) {
                $msg = sprintf('project-id %d: %s', $projectId, $e->getMessage());
                $this->logger->error($msg);
            }
        }
    }

}