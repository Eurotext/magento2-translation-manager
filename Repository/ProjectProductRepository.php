<?php

namespace Eurotext\TranslationManager\Repository;

use Eurotext\TranslationManager\Api\Data\ProjectProductInterface;
use Eurotext\TranslationManager\Api\ProjectProductRepositoryInterface;
use Eurotext\TranslationManager\Model\ProjectProduct;
use Eurotext\TranslationManager\Model\ProjectProductFactory;
use Eurotext\TranslationManager\Repository\Service\GetProjectProductListService;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProjectProductRepository implements ProjectProductRepositoryInterface
{
    /**
     * @var ProjectProductFactory
     */
    protected $projectProductFactory;

    /**
     * @var GetProjectProductListService
     */
    private $getProjectProductList;

    public function __construct(
        GetProjectProductListService $getProjectList,
        ProjectProductFactory $projectFactory
    ) {
        $this->projectProductFactory = $projectFactory;
        $this->getProjectProductList = $getProjectList;
    }

    /**
     * @param ProjectProductInterface $object
     *
     * @return ProjectProductInterface
     * @throws CouldNotSaveException
     */
    public function save(ProjectProductInterface $object): ProjectProductInterface
    {
        try {
            /** @var ProjectProduct $object */
            $object->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $object;
    }

    /**
     * @param int $id
     *
     * @return ProjectProductInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): ProjectProductInterface
    {
        /** @var ProjectProduct $object */
        $object = $this->projectProductFactory->create();
        $object->load($id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Project with id "%1" does not exist.', $id));
        }

        return $object;
    }

    /**
     * @param ProjectProductInterface $object
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ProjectProductInterface $object): bool
    {
        try {
            /** @var ProjectProduct $object */
            $object->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id): bool
    {
        $object = $this->getById($id);

        return $this->delete($object);
    }

    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface
    {
        return $this->getProjectProductList->execute($criteria);
    }
}
