<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Controller\Adminhtml\Project;

use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Delete Controller
 */
class Delete extends Action
{
    const ADMIN_RESOURCE = 'Eurotext_TranslationManager::project';

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository, ActionContext $context)
    {
        parent::__construct($context);
        $this->projectRepository = $projectRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultRedirectFactory->create();

        $projectId = $this->getRequest()->getPost('id');
        if ($projectId === null) {
            $this->messageManager->addErrorMessage(__('invalid request.'));

            return $result->setPath('*/*');
        }

        try {
            $this->projectRepository->deleteById((int)$projectId);

            $this->messageManager->addSuccessMessage(__('The Project has been deleted.'));
            $result->setPath('*/*');
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $result->setPath('*/*/edit', ['id' => $projectId, '_current' => true]);
        }

        return $result;
    }
}
