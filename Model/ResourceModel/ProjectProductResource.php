<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Model\ResourceModel;

use Eurotext\TranslationManager\Setup\EntitySchema\ProjectProductSchema;

class ProjectProductResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init(ProjectProductSchema::TABLE_NAME, ProjectProductSchema::ID);
    }
}
