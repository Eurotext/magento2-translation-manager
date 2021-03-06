<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Api;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;

interface EntityDataSaverInterface
{
    public function save(ProjectInterface $project, array &$data): bool;
}