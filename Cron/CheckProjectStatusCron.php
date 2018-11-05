<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Cron;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Service\Project\CheckProjectStatusServiceInterface;
use Eurotext\TranslationManager\Setup\EntitySchema\ProjectSchema;
use Magento\Framework\Api\SearchCriteriaBuilder;

class CheckProjectStatusCron
{

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var CheckProjectStatusServiceInterface
     */
    private $checkProjectStatus;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        CheckProjectStatusServiceInterface $checkProjectStatus
    ) {
        $this->projectRepository  = $projectRepository;
        $this->criteriaBuilder    = $criteriaBuilder;
        $this->checkProjectStatus = $checkProjectStatus;
    }

    public function execute()
    {
        // Get all open project
        $this->criteriaBuilder->addFilter(ProjectSchema::STATUS, ProjectInterface::STATUS_EXPORTED);
        $searchCriteria = $this->criteriaBuilder->create();
        $searchResults  = $this->projectRepository->getList($searchCriteria);

        /** @var ProjectInterface[] $projects */
        $projects = $searchResults->getItems();

        // Fetch Status from Eurotext
        foreach ($projects as $project) {
            $this->checkProjectStatus->execute($project);
        }
    }

}