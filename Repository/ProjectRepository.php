<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Repository;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Eurotext\TranslationManager\Model\ResourceModel\ProjectCollectionFactory;
use Eurotext\TranslationManager\Model\ResourceModel\ProjectResource;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * @var ProjectFactory
     */
    protected $projectFactory;

    /**
     * @var ProjectResource
     */
    private $projectResource;

    /**
     * @var ProjectCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    public function __construct(
        ProjectResource $productResource,
        ProjectFactory $projectFactory,
        ProjectCollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->projectFactory       = $projectFactory;
        $this->projectResource      = $productResource;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @param ProjectInterface $object
     *
     * @return ProjectInterface
     * @throws CouldNotSaveException
     */
    public function save(ProjectInterface $object): ProjectInterface
    {
        try {
            if (empty($object->getCode())) {
                $object->setCode(sprintf('project-%s', md5($object->getName())));
            }
            if (empty($object->getCreatedAt())) {
                $object->setCreatedAt(date('Y-m-d H:i:s'));
            }
            /** @var Project $object */
            $this->projectResource->save($object);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $object;
    }

    /**
     * @param int $id
     *
     * @return ProjectInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): ProjectInterface
    {
        /** @var Project $object */
        $object = $this->projectFactory->create();
        $this->projectResource->load($object, $id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Project with id "%1" does not exist.', $id));
        }

        return $object;
    }

    /**
     * @param ProjectInterface $object
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ProjectInterface $object): bool
    {
        try {
            /** @var Project $object */
            $this->projectResource->delete($object);
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
        /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection */
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields     = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition    = $filter->getConditionType() ?: 'eq';
                $fields[]     = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $direction = ($sortOrder->getDirection() === SortOrder::SORT_ASC) ? 'ASC' : 'DESC';
                $collection->addOrder($sortOrder->getField(), $direction);
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $objects = [];
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }

        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($objects);

        return $searchResults;
    }
}
