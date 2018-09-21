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

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        CreateProjectService $createProject,
        CreateProjectEntitiesService $createProjectEntities
    ) {
        $this->projectRepository     = $projectRepository;
        $this->createProject         = $createProject;
        $this->createProjectEntities = $createProjectEntities;
    }

    /**
     * @param int $id
     *
     * @return bool
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
        $entities = $this->createProjectEntities->execute($project);

        // Transfer finished, set Status to exported
        $project->setStatus(ProjectInterface::STATUS_EXPORTED);
        $this->projectRepository->save($project);

        return true;
    }
}
