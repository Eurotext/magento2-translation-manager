<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Controller\Adminhtml\Project;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Eurotext_Translationmanager::project';

    public function execute()
    {
        /** @var Page $result */
        $result = $this->resultFactory->create($this->resultFactory::TYPE_PAGE);
        $result->setActiveMenu('Eurotext_TranslationManager::project');
        $result->getConfig()->getTitle()->prepend(__('Eurotext TranslationManager - Projects'));

        return $result;
    }
}