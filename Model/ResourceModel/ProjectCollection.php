<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Model\ResourceModel;

use Eurotext\TranslationManager\Model\Project;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ProjectCollection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Project::class, ProjectResource::class);
    }
}
