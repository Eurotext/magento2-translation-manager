<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Controller\Adminhtml\Project;

use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Framework\View\Result\Page;

class Edit extends Action
{
    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectFactory $projectFactory,
        ActionContext $context
    ) {
        parent::__construct($context);

        $this->projectRepository = $projectRepository;
        $this->projectFactory    = $projectFactory;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        try {
            $project = $this->projectRepository->getById($id);
        } catch (\Exception $e) {
            // Project does not exist, create a new object
            $project = $this->projectFactory->create();
        }

        if ($id > 0 && empty($project->getId())) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('Project does not exist'));

            return $resultRedirect->setPath('eurotext_translationmanager/project');
        }

        /** @var Page $result */
        $result = $this->resultFactory->create($this->resultFactory::TYPE_PAGE);

        $result->setActiveMenu('Eurotext_TranslationManager::project');
        $result->getConfig()->getTitle()->prepend(__('Eurotext TranslationManager - Projects'));
        $result->getConfig()->getTitle()->prepend($project->getName());

        return $result;
    }
}