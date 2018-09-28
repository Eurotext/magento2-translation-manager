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
        // @todo Service: check API Project Status === finished

        // Projects need to be in status accepted otherwise they will not be received
        if ($project->getStatus() !== ProjectInterface::STATUS_ACCEPTED) {
            return false;
        }

        $entities = $this->fetchProjectEntities->execute($project);

        $this->projectStateMachine->apply($project, ProjectInterface::STATUS_IMPORTED);

        // @todo set API Project Status === imported

        return true;
    }
}
