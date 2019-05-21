<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Api;

interface EntityDataLoaderInterface
{
    public function load(int $projectId, array &$data): bool;
}