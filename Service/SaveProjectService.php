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
use Eurotext\TranslationManager\Entity\EntityDataSaverPool;
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

    /**
     * @var EntityDataSaverPool
     */
    private $entityDataSaverPool;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectFactory $projectFactory,
        EntityDataSaverPool $entityDataSaverPool,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->projectRepository   = $projectRepository;
        $this->projectFactory      = $projectFactory;
        $this->dataObjectHelper    = $dataObjectHelper;
        $this->entityDataSaverPool = $entityDataSaverPool;
    }

    /**
     * @inheritdoc
     */
    public function saveByRequest(HttpRequest $request): ProjectInterface
    {
        $project = $this->saveProject($request);

        $this->saveEntitiesData($project, $request);

        return $project;
    }

    /**
     * @param HttpRequest $request
     *
     * @return ProjectInterface
     * @throws InvalidRequestException
     * @throws PersistanceException
     */
    private function saveProject(HttpRequest $request): ProjectInterface
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

        // Save Project
        try {
            if ($id > 0) {
                $project = $this->projectRepository->getById($id);
            } else {
                $project = $this->projectFactory->create($request->getParams());
                $project->setStatus(ProjectInterface::STATUS_NEW);
            }

            $this->dataObjectHelper->populateWithArray($project, $requestGeneral, ProjectInterface::class);

            $this->projectRepository->save($project);

        } catch (NoSuchEntityException $e) {
            throw new PersistanceException($id, 'The Project does not exist.', $e->getCode(), $e);
        } catch (CouldNotSaveException $e) {
            throw new PersistanceException($id, $e->getMessage(), $e->getCode(), $e->getPrevious());
        } catch (\Exception $e) {
            throw new PersistanceException($id, 'Project could not be saved.', $e->getCode(), $e);
        }

        return $project;
    }

    /**
     * @param ProjectInterface $project
     * @param HttpRequest $request
     *
     * @throws PersistanceException
     */
    private function saveEntitiesData(ProjectInterface $project, HttpRequest $request)
    {
        $requestData = $request->getParams();

        $errors = [];
        foreach ($this->entityDataSaverPool->getItems() as $entityType => $entityDataSaver) {
            try {
                $result = $entityDataSaver->save($project, $requestData);

                if ($result === false) {
                    $errors[$entityType] = ucfirst($entityType) .
                        ': Errors during persistance, see var/log/eurotext_api.log for further details.';
                }
            } catch (\Exception $e) {
                $errors[$entityType] = $entityType . ': ' . $e->getMessage();
            }
        }

        if (count($errors)) {
            throw new PersistanceException($project->getId(), implode("\n", $errors));
        }
    }

}
