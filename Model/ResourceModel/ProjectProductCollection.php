<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Model\ResourceModel;

use Eurotext\TranslationManager\Model\ProjectProduct;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ProjectProductCollection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(ProjectProduct::class, ProjectProductResource::class);
    }
}
