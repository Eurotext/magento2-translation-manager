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
use Psr\Log\LoggerInterface;

class SendProjectService
{
    /**
     * @var \Eurotext\TranslationManager\Api\ProjectRepositoryInterface
     */
    private $projectRepository;

    /** @var CreateProjectService */
    private $createProject;

    /** @var CreateProjectEntitiesService */
    private $createProjectEntities;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        CreateProjectService $createProject,
        CreateProjectEntitiesService $createProjectEntities,
        LoggerInterface $logger
    ) {
        $this->projectRepository     = $projectRepository;
        $this->createProject         = $createProject;
        $this->createProjectEntities = $createProjectEntities;
    }

    public function executeById(int $id) /** return-types not supported by magento code-generator */
    {
        $project = $this->projectRepository->getById($id);

        return $this->execute($project);
    }

    public function execute(ProjectInterface $project) /** return-types not supported by magento code-generator */
    {
        $result = [];

        // Create Project
        $projectCreated    = $this->createProject->execute($project);
        $result['project'] = 1;
        if ($projectCreated === false) {
            $result['project'] = 'error creating project';

            return $result;
        }

        // Create Entities
        $entitiesCreated = $this->createProjectEntities->execute($project);

        $result = array_merge($result, $entitiesCreated);

        return $result;
    }
}
