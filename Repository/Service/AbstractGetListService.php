<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Repository\Service;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

abstract class AbstractGetListService
{
    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * AbstractGetListService constructor.
     *
     * @param \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(\Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory)
    {
        $this->searchResultsFactory = $searchResultsFactory;
    }

    abstract protected function createCollection(): AbstractCollection;

    public function execute(SearchCriteriaInterface $criteria): SearchResultsInterface
    {
        $collection = $this->createCollection();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $fields[] = $filter->getField();
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
