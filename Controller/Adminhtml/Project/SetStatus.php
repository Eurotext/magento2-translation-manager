<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Controller\Adminhtml\Project;

use Eurotext\TranslationManager\State\ProjectStateMachine;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\ResultInterface;

class SetStatus extends Action
{
    const ADMIN_RESOURCE = 'Eurotext_TranslationManager::project';

    /**
     * @var ProjectStateMachine
     */
    private $projectStateMachine;

    public function __construct(Context $context, ProjectStateMachine $projectStateMachine)
    {
        parent::__construct($context);
        $this->projectStateMachine = $projectStateMachine;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        /** @var HttpRequest $request */
        $request = $this->getRequest();

        try {
            $projectId = (int)$request->getParam('id');
            $status    = $request->getParam('status');

            if (empty($status) || empty($projectId)) {
                $result = $this->resultRedirectFactory->create();
                $result->setPath('*/*/');

                return $result;
            }

            $this->projectStateMachine->applyById($projectId, $status);

            $this->messageManager->addSuccessMessage(__('The Project status has been updated to %1.', $status));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $result = $this->resultRedirectFactory->create();
        $result->setPath('*/*/');

        return $result;
    }

}
