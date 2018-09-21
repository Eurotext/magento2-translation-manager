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

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        FetchProjectEntitiesService $fetchProjectEntities
    ) {
        $this->projectRepository    = $projectRepository;
        $this->fetchProjectEntities = $fetchProjectEntities;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function executeById(int $id) // return-types not supported by magento code-generator
    {
        $project = $this->projectRepository->getById($id);

        return $this->execute($project);
    }

    /**
     * @param ProjectInterface $project
     *
     * @return array
     */
    public function execute(ProjectInterface $project) // return-types not supported by magento code-generator
    {
        $result = [];

        // Projects need to be in status accepted otherwise they will not be received
        if ($project->getStatus() !== ProjectInterface::STATUS_ACCEPTED) {
            return [];
        }

        $entities = $this->fetchProjectEntities->execute($project);

        $result = array_merge($result, $entities);

        $project->setStatus(ProjectInterface::STATUS_IMPORTED);
        $this->projectRepository->save($project);

        // @todo set API Project Status === imported

        return $result;
    }
}
