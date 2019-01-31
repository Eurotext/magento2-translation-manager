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

class ProjectEditDataProvider extends AbstractDataProvider
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

    public function getData()
    {
        $data = parent::getData();

        if ($data['totalRecords'] > 0) {
            $item = $data['items'][0];

            $data = [
                $item['id'] => [
                    'general'  => $item,
                    'products' => [
                    ],
                ],
            ];
        }

        return $data;
    }
}