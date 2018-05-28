<?php

namespace Eurotext\TranslationManager\Repository;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Eurotext\TranslationManager\Repository\Service\GetProjectListService;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProjectRepository implements ProjectRepositoryInterface
{
    protected $objectFactory;

    /**
     * @var \Eurotext\TranslationManager\Repository\Service\GetProjectListService
     */
    private $getProjectList;

    public function __construct(
        GetProjectListService $getProjectList,
        ProjectFactory $objectFactory
    ) {
        $this->objectFactory = $objectFactory;
        $this->getProjectList = $getProjectList;
    }

    /**
     * @param \Eurotext\TranslationManager\Api\Data\ProjectInterface $project
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ProjectInterface $project)
    {
        try {
            /** @var Project $project */
            $project->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $project;
    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        /** @var Project $object */
        $object = $this->objectFactory->create();
        $object->load($id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Project with id "%1" does not exist.', $id));
        }

        return $object;
    }

    /**
     * @param \Eurotext\TranslationManager\Api\Data\ProjectInterface $project
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ProjectInterface $project)
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
     * @param $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($id)
    {
        $project = $this->getById($id);

        return $this->delete($project);
    }

    public function getList(SearchCriteriaInterface $criteria)
    {
        return $this->getProjectList->execute($criteria);
    }
}
