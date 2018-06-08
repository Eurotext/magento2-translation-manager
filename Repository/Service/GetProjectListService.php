<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Repository\Service;

use Eurotext\TranslationManager\Model\ResourceModel\ProjectCollectionFactory;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

class GetProjectListService extends AbstractGetListService
{
    /**
     * @var ProjectCollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        ProjectCollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        parent::__construct($searchResultsFactory);

        $this->collectionFactory = $collectionFactory;
    }

    protected function createCollection(): \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
    {
        return $this->collectionFactory->create();
    }
}
