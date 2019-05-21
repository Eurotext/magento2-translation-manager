<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service\Project;

use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Validator\ProjectStatusValidatorInterface;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Psr\Log\LoggerInterface;

class CheckProjectStatusService implements CheckProjectStatusServiceInterface
{
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

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectStatusValidatorInterface $projectStatusValidator,
        ProjectStateMachine $projectStateMachine,
        LoggerInterface $logger
    ) {
        $this->projectRepository      = $projectRepository;
        $this->projectStatusValidator = $projectStatusValidator;
        $this->projectStateMachine    = $projectStateMachine;
        $this->logger                 = $logger;
    }

    public function executeById(int $projectId)
    {
        $project = $this->projectRepository->getById($projectId);

        return $this->execute($project);
    }

    public function execute(ProjectInterface $project)
    {
        $result = true;

        // check API Project Status === finished
        $requiredStatus = ProjectStatusEnum::FINISHED();
        $isFinished     = $this->projectStatusValidator->validate($project, $requiredStatus);

        if (!$isFinished) {
            // skip project if not finished
            return $result;
        }

        // Update Status
        $id            = $project->getId();
        $currentStatus = $project->getStatus();
        $newStatus     = ProjectInterface::STATUS_TRANSLATED;
        try {
            $this->projectStateMachine->apply($project, $newStatus);

            $msg = sprintf('project id=%s: status change %s => %s', $id, $currentStatus, $newStatus);
            $this->logger->info($msg);
        } catch (IllegalProjectStatusChangeException $e) {
            $msg = sprintf('project id=%s: illegal status change %s => %s', $id, $currentStatus, $newStatus);
            $this->logger->error($msg);
            $result = false;
        } catch (InvalidProjectStatusException $e) {
            $msg = sprintf('project id=%s: invalid status %s', $id, $newStatus);
            $this->logger->error($msg);
            $result = false;
        }

        return $result;
    }
}