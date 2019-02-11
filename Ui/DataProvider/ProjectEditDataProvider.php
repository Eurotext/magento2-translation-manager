<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Ui\DataProvider;

use Eurotext\TranslationManager\Model\ResourceModel\ProjectCollectionFactory;
use Eurotext\TranslationManager\Ui\EntityDataLoader\EntityDataLoaderPool;
use Magento\Ui\DataProvider\AbstractDataProvider;

class ProjectEditDataProvider extends AbstractDataProvider
{
    /**
     * @var EntityDataLoaderPool
     */
    private $entityDataLoaderPool;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ProjectCollectionFactory $projectCollectionFactory,
        EntityDataLoaderPool $entityDataLoaderPool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection           = $projectCollectionFactory->create();
        $this->entityDataLoaderPool = $entityDataLoaderPool;
    }

    public function getData()
    {
        $data = parent::getData();

        if ($data['totalRecords'] > 0) {
            $item = $data['items'][0];

            $projectId = (int) $item['id'];

            $projectData = [
                'project' => $item,
            ];

            foreach ($this->entityDataLoaderPool->getItems() as $entityDataLoader) {
                $entityDataLoader->load($projectId, $projectData);
            }

            $data = [
                $projectId => $projectData,
            ];
        }

        return $data;
    }
}