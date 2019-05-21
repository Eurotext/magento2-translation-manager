<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service\Project;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;

interface CreateProjectEntitiesServiceInterface
{
    public function execute(ProjectInterface $project): array;
}