<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Cron;

use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Validator\ProjectStatusValidatorInterface;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\Setup\EntitySchema\ProjectSchema;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;

class CheckProjectStatusCron
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
     * @var ProjectStatusValidatorInterface
     */
    private $projectStatusValidator;

    /**
     * @var ProjectStateMachine
     */
    private $projectStateMachine;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        ProjectStatusValidatorInterface $projectStatusValidator,
        ProjectStateMachine $projectStateMachine,
        LoggerInterface $logger
    ) {
        $this->projectRepository      = $projectRepository;
        $this->criteriaBuilder        = $criteriaBuilder;
        $this->projectStatusValidator = $projectStatusValidator;
        $this->projectStateMachine    = $projectStateMachine;
        $this->logger                 = $logger;
    }

    public function execute()
    {
        // Get all open project
        $this->criteriaBuilder->addFilter(ProjectSchema::STATUS, ProjectInterface::STATUS_EXPORTED);
        $searchCriteria = $this->criteriaBuilder->create();
        $searchResults  = $this->projectRepository->getList($searchCriteria);

        /** @var ProjectInterface[] $projects */
        $projects = $searchResults->getItems();

        // Fetch Status from Eurotext
        foreach ($projects as $project) {
            $this->updateProjectStatus($project);
        }
    }

    private function updateProjectStatus(ProjectInterface $project)
    {
        // check API Project Status === finished
        $requiredStatus = ProjectStatusEnum::FINISHED();
        $isFinished     = $this->projectStatusValidator->validate($project, $requiredStatus);

        if (!$isFinished) {
            // skip project if not finished
            return;
        }

        // Update Status
        $currentStatus = $project->getStatus();
        $newStatus     = ProjectInterface::STATUS_TRANSLATED;
        $id            = $project->getId();
        try {
            $this->projectStateMachine->apply($project, $newStatus);
        } catch (IllegalProjectStatusChangeException $e) {
            $msg = sprintf('project id=%s: illegal status change %s => %s', $id, $currentStatus, $newStatus);
            $this->logger->error($msg);
        } catch (InvalidProjectStatusException $e) {
            $msg = sprintf('project id=%s: invalid status %s', $id, $newStatus);
            $this->logger->error($msg);
        }
    }
}