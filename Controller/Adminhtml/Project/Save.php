<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Controller\Adminhtml\Project;

use Eurotext\TranslationManager\Exception\InvalidRequestException;
use Eurotext\TranslationManager\Exception\PersistanceException;
use Eurotext\TranslationManager\Service\SaveProjectServiceInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect as RedirectResult;
use Magento\Framework\Controller\ResultInterface;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Eurotext_Translationmanager::project';

    /**
     * @var SaveProjectServiceInterface
     */
    private $saveProjectService;

    public function __construct(Context $context, SaveProjectServiceInterface $saveProjectService)
    {
        parent::__construct($context);
        $this->saveProjectService = $saveProjectService;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        /** @var HttpRequest $request */
        $request = $this->getRequest();

        try {
            $project = $this->saveProjectService->saveByRequest($request);

            $this->messageManager->addSuccessMessage(__('The Project has been saved.'));
            $result = $this->redirectAfterSuccess($project->getId());
        } catch (InvalidRequestException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $result = $this->redirectAfterFailure();
        } catch (PersistanceException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $result = $this->redirectAfterFailure($e->getEntityId());
        }

        return $result;
    }

    private function redirectAfterSuccess(int $projectId): RedirectResult
    {
        $result = $this->resultRedirectFactory->create();
        if ($this->getRequest()->getParam('back')) {
            $result->setPath('*/*/edit', ['id' => $projectId, '_current' => true]);
        } else {
            $result->setPath('*/*/');
        }

        return $result;
    }

    private function redirectAfterFailure(int $projectId = 0): RedirectResult
    {
        $result = $this->resultRedirectFactory->create();
        if ($projectId === 0) {
            $result->setPath('*/*/new');
        } else {
            $result->setPath('*/*/edit', ['id' => $projectId, '_current' => true,]);
        }

        return $result;
    }
}
