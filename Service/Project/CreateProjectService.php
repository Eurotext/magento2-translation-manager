<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service\Project;

use Eurotext\RestApiClient\Api\ProjectV1ApiInterface;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Mapper\ProjectPostMapper;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class CreateProjectService implements CreateProjectServiceInterface
{
    /** @var ProjectPostMapper */
    private $projectPostMapper;

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var ProjectV1ApiInterface
     */
    private $projectApi;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectPostMapper $projectPostMapper,
        ProjectV1ApiInterface $projectApi,
        LoggerInterface $logger
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectApi        = $projectApi;
        $this->projectPostMapper = $projectPostMapper;
        $this->logger            = $logger;
    }

    public function executeById(int $id): bool
    {
        $project = $this->projectRepository->getById($id);

        return $this->execute($project);
    }

    public function execute(ProjectInterface $project): bool
    {
        $id    = $project->getId();
        $extId = $project->getExtId();

        if ($extId > 0) {
            return true;
        }

        $this->logger->info(sprintf('send project post for id:%d', $id));

        $request = $this->projectPostMapper->map($project);

        try {
            // create project via ApiClient
            $response = $this->projectApi->post($request);

            $this->logger->info(sprintf('project id:%d => success', $id));
        } catch (GuzzleException $apiException) {
            $e = $apiException->getPrevious();

            $message = $e->getMessage();
            $this->logger->error(sprintf('project id:%d => %s', $id, $message));
            $project->setLastError($message);

            return false;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error(sprintf('project id:%d => %s', $id, $message));
            $project->setLastError($message);

            return false;
        }

        // save project ext_id
        $extId = $response->getId();
        $project->setExtId($extId);

        $this->projectRepository->save($project);
        $this->logger->info(sprintf('project id:%d ext-id:%d saved', $id, $extId));

        return true;
    }
}