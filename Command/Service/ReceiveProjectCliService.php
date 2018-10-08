<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Command\Service;

use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Validator\ProjectStatusValidatorInterface;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Service\ReceiveProjectService;
use Eurotext\TranslationManager\State\ProjectStateMachine;

class ReceiveProjectCliService
{
    /**
     * @var ReceiveProjectService
     */
    private $receiveProject;

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

    public function __construct(
        ReceiveProjectService $receiveProject,
        ProjectRepositoryInterface $projectRepository,
        ProjectStatusValidatorInterface $projectStatusValidator,
        ProjectStateMachine $projectStateMachine
    ) {
        $this->receiveProject         = $receiveProject;
        $this->projectRepository      = $projectRepository;
        $this->projectStatusValidator = $projectStatusValidator;
        $this->projectStateMachine    = $projectStateMachine;
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
        $isFinished = $this->projectStatusValidator->validate($project, ProjectStatusEnum::FINISHED());
        if (!$isFinished) {
            return false;
        }

        // Set status ACCEPTED, because services are checking for the correct workflow
        $this->projectStateMachine->apply($project, ProjectInterface::STATUS_TRANSLATED);
        $this->projectStateMachine->apply($project, ProjectInterface::STATUS_ACCEPTED);

        return $this->receiveProject->execute($project);
    }
}