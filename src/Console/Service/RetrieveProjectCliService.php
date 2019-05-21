<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Console\Service;

use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Validator\ProjectStatusValidatorInterface;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Logger\ApiLogger;
use Eurotext\TranslationManager\Service\RetrieveProjectServiceInterface;
use Eurotext\TranslationManager\State\ProjectStateMachine;

class RetrieveProjectCliService
{
    /**
     * @var RetrieveProjectServiceInterface
     */
    private $retrieveProject;

    /**
     * @var ProjectStateMachine
     */
    private $projectStateMachine;

    /**
     * @var ProjectStatusValidatorInterface
     */
    private $projectStatusValidator;

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var ApiLogger
     */
    private $logger;

    public function __construct(
        RetrieveProjectServiceInterface $retrieveProject,
        ProjectRepositoryInterface $projectRepository,
        ProjectStatusValidatorInterface $projectStatusValidator,
        ProjectStateMachine $projectStateMachine,
        ApiLogger $logger
    ) {
        $this->retrieveProject         = $retrieveProject;
        $this->projectRepository      = $projectRepository;
        $this->projectStatusValidator = $projectStatusValidator;
        $this->projectStateMachine    = $projectStateMachine;
        $this->logger                 = $logger;
    }

    /**
     * @param int $projectId
     *
     * @return bool
     * @throws \Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException
     * @throws \Eurotext\TranslationManager\Exception\InvalidProjectStatusException
     */
    public function executeById(int $projectId)
    {
        // Load Project
        $project = $this->projectRepository->getById($projectId);

        // check API Project Status === finished
        $requiredStatus = ProjectStatusEnum::FINISHED();
        $isFinished     = $this->projectStatusValidator->validate($project, $requiredStatus);
        if (!$isFinished) {
            $this->logger->error("project api-status is not $requiredStatus");

            return false;
        }

        // Set status ACCEPTED, because services are checking for the correct workflow
        $this->projectStateMachine->apply($project, ProjectInterface::STATUS_TRANSLATED);
        $this->projectStateMachine->apply($project, ProjectInterface::STATUS_ACCEPTED);

        return $this->retrieveProject->execute($project);
    }
}