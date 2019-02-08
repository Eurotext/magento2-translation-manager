<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Controller\Adminhtml\Project;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect as RedirectResult;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Save Controller
 */
class Save extends Action
{
    const ADMIN_RESOURCE = 'Eurotext_Translationmanager::project';

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        Context $context,
        ProjectRepositoryInterface $projectRepository,
        ProjectFactory $projectFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context);
        $this->projectRepository = $projectRepository;
        $this->projectFactory    = $projectFactory;
        $this->dataObjectHelper  = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        $requestData = $request->getPost()->toArray();

        if (empty($requestData['project']) || !$request->isPost()) {
            $this->messageManager->addErrorMessage(__('invalid request.'));

            return $this->redirectAfterFailure();
        }

        $requestGeneral = $requestData['project'];

        $id = 0;
        if (isset($requestGeneral['id'])) {
            $id = (int)$requestGeneral['id'];
        }

        try {
            if ($id > 0) {
                $project = $this->projectRepository->getById($id);
            } else {
                $project = $this->projectFactory->create($request->getParams());
            }
            // @todo optimize Processing of Project save
            $this->dataObjectHelper->populateWithArray($project, $requestGeneral, ProjectInterface::class);

            $this->projectRepository->save($project);

            $this->messageManager->addSuccessMessage(__('The Project has been saved.'));
            $result = $this->redirectAfterSuccess($project->getId());
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The Project does not exist.'));
            $result = $this->redirectAfterFailure();
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $result = $this->redirectAfterFailure($id);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Project could not be saved.'));
            $result = $this->redirectAfterFailure($id);
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
