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
        $this->projectStateMachine = $projectStateMachine;
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException
     * @throws \Eurotext\TranslationManager\Exception\InvalidProjectStatusException
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
     * @throws \Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException
     * @throws \Eurotext\TranslationManager\Exception\InvalidProjectStatusException
     */
    public function execute(ProjectInterface $project) // return-types not supported by magento code-generator
    {
        // Projects may only be created if they are in status transfer
        if ($project->getStatus() !== ProjectInterface::STATUS_TRANSFER) {
            return false;
        }

        // Send Project to Api
        $projectCreated = $this->createProject->execute($project);

        if ($projectCreated === false) {
            return false;
        }

        // Send Entities to Api
        $this->createProjectEntities->execute($project);

        // Transfer finished, set Status to exported
        $this->projectStateMachine->apply($project, ProjectInterface::STATUS_EXPORTED);

        return true;
    }
}
