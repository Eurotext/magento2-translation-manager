<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Repository;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Eurotext\TranslationManager\Repository\Service\GetProjectListService;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProjectRepository implements ProjectRepositoryInterface
{
    const CODE_UNIQUE_ID_PREFIX = 'etm2_project_';

    /**
     * @var \Eurotext\TranslationManager\Model\ProjectFactory
     */
    protected $projectFactory;

    /**
     * @var \Eurotext\TranslationManager\Repository\Service\GetProjectListService
     */
    private $getProjectList;

    public function __construct(
        GetProjectListService $getProjectList,
        ProjectFactory $projectFactory
    ) {
        $this->projectFactory = $projectFactory;
        $this->getProjectList = $getProjectList;
    }

    /**
     * @param \Eurotext\TranslationManager\Api\Data\ProjectInterface $project
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ProjectInterface $project): ProjectInterface
    {
        if ($project->getCode() === '') {
            $project->setCode(uniqid(self::CODE_UNIQUE_ID_PREFIX, true));
        }

        try {
            /** @var Project $project */
            $project->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $project;
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): ProjectInterface
    {
        /** @var Project $project */
        $project = $this->projectFactory->create();
        $project->load($id);
        if (!$project->getId()) {
            throw new NoSuchEntityException(__('Project with id "%1" does not exist.', $id));
        }

        return $project;
    }

    /**
     * @param \Eurotext\TranslationManager\Api\Data\ProjectInterface $project
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ProjectInterface $project): bool
    {
        try {
            /** @var Project $project */
            $project->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $id): bool
    {
        $project = $this->getById($id);

        return $this->delete($project);
    }

    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface
    {
        return $this->getProjectList->execute($criteria);
    }
}
