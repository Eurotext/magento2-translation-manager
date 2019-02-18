<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Exception\InvalidRequestException;
use Eurotext\TranslationManager\Exception\PersistanceException;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class SaveProjectService implements SaveProjectServiceInterface
{
    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectFactory $projectFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectFactory    = $projectFactory;
        $this->dataObjectHelper  = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     */
    public function saveByRequest(HttpRequest $request): ProjectInterface
    {
        $requestData = $request->getParams();

        if (empty($requestData['project']) || !$request->isPost()) {
            throw new InvalidRequestException('project data not found');
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

            $this->dataObjectHelper->populateWithArray($project, $requestGeneral, ProjectInterface::class);

            $this->projectRepository->save($project);

        } catch (NoSuchEntityException $e) {
            throw new PersistanceException($id, 'The Project does not exist.');
        } catch (CouldNotSaveException $e) {
            throw new PersistanceException($id, $e->getMessage());
        } catch (\Exception $e) {
            throw new PersistanceException($id, 'Project could not be saved.');
        }

        return $project;
    }

}
