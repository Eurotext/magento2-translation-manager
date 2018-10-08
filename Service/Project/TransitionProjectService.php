<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service\Project;

use Eurotext\RestApiClient\Api\ProjectV1ApiInterface;
use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Request\ProjectTransitionRequest;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class TransitionProjectService
{
    private $allowedStatusCode = [200, 201, 202, 203, 204];

    /**
     * @var ProjectV1ApiInterface
     */
    private $projectApi;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ProjectV1ApiInterface $projectApi, LoggerInterface $logger)
    {
        $this->projectApi = $projectApi;
        $this->logger     = $logger;
    }

    public function execute(ProjectInterface $project, ProjectStatusEnum $status): bool
    {
        $projectId = $project->getExtId();

        $request = new ProjectTransitionRequest($projectId, $status);

        try {
            $response = $this->projectApi->transition($request);
        } catch (GuzzleException $e) {
            $message = $e->getMessage();
            $this->logger->error(sprintf('project id:%d => %s', $projectId, $message));
            $project->setLastError($message);

            return false;
        }

        $httpResponse = $response->getHttpResponse();
        $statusCode   = $httpResponse->getStatusCode();

        $result = true;
        if (!\in_array($statusCode, $this->allowedStatusCode, true)) {
            $project->setLastError("Invalid HTTP response status code: $statusCode");
            $result = false;
        }

        return $result;
    }
}