<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;
use Eurotext\TranslationManager\Service\Project\CreateProjectEntitiesService;
use Eurotext\TranslationManager\Service\Project\CreateProjectService;
use Eurotext\TranslationManager\State\ProjectStateMachine;

class SendProjectService
{
    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /** @var CreateProjectService */
    private $createProject;

    /** @var CreateProjectEntitiesService */
    private $createProjectEntities;

    /**
     * @var ProjectStateMachine
     */
    private $projectStateMachine;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        CreateProjectService $createProject,
        CreateProjectEntitiesService $createProjectEntities,
        ProjectStateMachine $projectStateMachine
    ) {
        $this->projectRepository     = $projectRepository;
        $this->createProject         = $createProject;
        $this->createProjectEntities = $createProjectEntities;
        $this->projectStateMachine   = $projectStateMachine;
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws IllegalProjectStatusChangeException
     * @throws InvalidProjectStatusException
     */
    public function executeById(int $id) // return-types not supported by magento code-generator
    {
        $project = $this->projectRepository->getById($id);

        return $this->execute($project);
    }

    /**
     * @param ProjectInterface $project
     *
     * @return bool
     * @throws IllegalProjectStatusChangeException
     * @throws InvalidProjectStatusException
     */
    public function execute(ProjectInterface $project) // return-types not supported by magento code-generator
    {
        // Projects may only be created if they are in status transfer
        if ($project->getStatus() !== ProjectInterface::STATUS_TRANSFER) {
            throw new InvalidProjectStatusException(
                sprintf(
                    'project needs to be in status "%s", current status is "%s"',
                    ProjectInterface::STATUS_TRANSFER, $project->getStatus()
                )
            );
        }

        // Send Project to Api
        $resultProject = $this->createProject->execute($project);

        if ($resultProject === false) {
            return false;
        }

        // Send Entities to Api
        $resultEntities = $this->createProjectEntities->execute($project);

        // Check results for error messages
        $status = ProjectInterface::STATUS_EXPORTED;
        if ($this->validateResultEntities($resultEntities) === true) {
            $status = ProjectInterface::STATUS_ERROR;
        }

        // Transfer finished, set Status
        $this->projectStateMachine->apply($project, $status);

        return true;
    }

    /**
     * @param $resultEntities
     *
     * @return bool
     */
    private function validateResultEntities($resultEntities): bool
    {
        $hasErrors = false;
        foreach ($resultEntities as $entityKey => $entityResult) {
            if ($entityResult !== true) {
                $hasErrors = true;
            }
        }

        return $hasErrors;
    }
}
