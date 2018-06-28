<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Model\ResourceModel;

use Eurotext\TranslationManager\Setup\EntitySchema\ProjectSchema;

class ProjectResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init(ProjectSchema::TABLE_NAME, ProjectSchema::ID);
    }
}
