<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Ui\DataProvider;

use Eurotext\TranslationManager\Model\ResourceModel\ProjectCollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class ProjectListingDataProvider extends AbstractDataProvider
{
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ProjectCollectionFactory $projectCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $projectCollectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function __getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        $data = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items'        => array_values($items),
        ];

        return $data;
    }

}