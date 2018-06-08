<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Api;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;

interface EntityReceiverInterface
{
    public function receive(ProjectInterface $project): bool;
}