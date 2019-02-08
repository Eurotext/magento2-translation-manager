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

            $products = [
                1 => [
                    'entity_id' => 1,
                    'id'        => 1,
                    'sku'       => '234234234',
                    'name'      => 'Hans',
                    'status'    => 'enabled',
                    'position'  => '1',
                ],
                2 => [
                    'entity_id' => 2,
                    'id'        => 2,
                    'sku'       => '234234234',
                    'name'      => 'Hans',
                    'status'    => 'enabled',
                    'position'  => '1',
                ],
            ];

            $item['products'] = $products;

//            $item['eurotext_project_product_listing'] = $products;

            $data = [
                $item['id'] => [
                    'project'  => $item,
                    'products' => $products,

//                    'eurotext_project_product_listing' => $products,
                ],
            ];
        }

        return $data;
    }
}