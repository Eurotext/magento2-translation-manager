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
use Eurotext\TranslationManager\Service\ReceiveProjectService;
use Eurotext\TranslationManager\Setup\EntitySchema\ProjectSchema;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;

class ReceiveProjectsCron
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
     * @var ReceiveProjectService
     */
    private $receiveProjectService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        ReceiveProjectService $receiveProjectService,
        LoggerInterface $logger
    ) {
        $this->projectRepository     = $projectRepository;
        $this->criteriaBuilder       = $criteriaBuilder;
        $this->receiveProjectService = $receiveProjectService;
        $this->logger                = $logger;
    }

    public function execute()
    {
        // Get all accpted projects
        $this->criteriaBuilder->addFilter(ProjectSchema::STATUS, ProjectInterface::STATUS_ACCEPTED);
        $searchCriteria = $this->criteriaBuilder->create();
        $searchResults  = $this->projectRepository->getList($searchCriteria);

        /** @var ProjectInterface[] $projects */
        $projects = $searchResults->getItems();

        // Receive Project from Eurotext
        foreach ($projects as $project) {
            $projectId = $project->getId();
            try {
                $result = $this->receiveProjectService->execute($project);
                if ($result === false) {
                    $msg = sprintf('project-id %d: error receiving the project', $projectId);
                    $this->logger->error($msg);
                }
            } catch (\Exception $e) {
                $msg = sprintf('project-id %d: %s', $projectId, $e->getMessage());
                $this->logger->error($msg);
            }
        }
    }

}