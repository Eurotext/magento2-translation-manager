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
use Eurotext\TranslationManager\Service\Project\FetchProjectEntitiesService;
use Eurotext\TranslationManager\State\ProjectStateMachine;

class ReceiveProjectService
{
    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var FetchProjectEntitiesService
     */
    private $fetchProjectEntities;

    /**
     * @var ProjectStateMachine
     */
    private $projectStateMachine;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        FetchProjectEntitiesService $fetchProjectEntities,
        ProjectStateMachine $projectStateMachine
    ) {
        $this->projectRepository    = $projectRepository;
        $this->fetchProjectEntities = $fetchProjectEntities;
        $this->projectStateMachine  = $projectStateMachine;
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
        // Projects need to be in status accepted otherwise they will not be received
        if ($project->getStatus() !== ProjectInterface::STATUS_ACCEPTED) {
            throw new InvalidProjectStatusException(
                sprintf(
                    'project needs to be in status "%s", current status is "%s"',
                    ProjectInterface::STATUS_ACCEPTED, $project->getStatus()
                )
            );
        }

        $resultEntities = $this->fetchProjectEntities->execute($project);

        // Check results for error messages
        $status = ProjectInterface::STATUS_IMPORTED;
        if ($this->validateResultEntities($resultEntities) === true) {
            $status = ProjectInterface::STATUS_ERROR;
        }

        // Transfer finished, set Status
        $this->projectStateMachine->apply($project, $status);

        // @todo set API Project Status === imported

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
