<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service\Project;

use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;

interface TransitionProjectServiceInterface
{
    public function execute(ProjectInterface $project, ProjectStatusEnum $status): bool;
}