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

        $entities = $this->fetchProjectEntities->execute($project);

        $result = array_merge($result, $entities);

        return $result;
    }
}
