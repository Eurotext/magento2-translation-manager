<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Controller\Adminhtml\Project;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Framework\View\Result\Page;

class Index extends Action
{
    public function __construct(ActionContext $context)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Page $result */
        $result = $this->resultFactory->create($this->resultFactory::TYPE_PAGE);

        $result->setActiveMenu('Eurotext_TranslationManager::project');
        $result->getConfig()->getTitle()->prepend(__('Eurotext TranslationManager - Projects'));

        return $result;
    }
}