<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\State;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException;
use Eurotext\TranslationManager\Exception\InvalidProjectStatusException;

class ProjectStateMachine
{
    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /** @var array */
    private $transitions = [
        ProjectInterface::STATUS_NEW        => [ProjectInterface::STATUS_TRANSFER],
        ProjectInterface::STATUS_TRANSFER   => [ProjectInterface::STATUS_EXPORTED, ProjectInterface::STATUS_ERROR],
        ProjectInterface::STATUS_EXPORTED   => [ProjectInterface::STATUS_TRANSLATED],
        ProjectInterface::STATUS_TRANSLATED => [ProjectInterface::STATUS_ACCEPTED, ProjectInterface::STATUS_ERROR],
        ProjectInterface::STATUS_ACCEPTED   => [ProjectInterface::STATUS_IMPORTED, ProjectInterface::STATUS_TRANSLATED],
        ProjectInterface::STATUS_ERROR      => [
            ProjectInterface::STATUS_TRANSFER,
            ProjectInterface::STATUS_EXPORTED,
            ProjectInterface::STATUS_ACCEPTED,
            ProjectInterface::STATUS_IMPORTED,
        ],
    ];

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @param int $projectId
     * @param string $status
     *
     * @throws InvalidProjectStatusException
     * @throws IllegalProjectStatusChangeException
     */
    public function applyById(int $projectId, string $status)
    {
        $project = $this->projectRepository->getById($projectId);

        $this->apply($project, $status);
    }

    /**
     * @param ProjectInterface $project
     * @param string $status
     *
     * @throws InvalidProjectStatusException
     * @throws IllegalProjectStatusChangeException
     */
    public function apply(ProjectInterface $project, string $status)
    {
        $id            = $project->getId();
        $currentStatus = $project->getStatus();

        if ($currentStatus === $status) {
            return;
        }

        if (!array_key_exists($currentStatus, $this->transitions)) {
            $msg = sprintf('unknown status="%s" (project-id=%d)', $currentStatus, $id);
            throw new InvalidProjectStatusException($msg);
        }

        $allowedStatuses = $this->transitions[$currentStatus];

        if (!\in_array($status, $allowedStatuses, true)) {
            $msg = sprintf('illegal status-change from "%s" to "%s" (project-id=%d)', $currentStatus, $status, $id);
            throw new IllegalProjectStatusChangeException($msg);
        }

        $project->setStatus($status);

        $this->projectRepository->save($project);
    }
}