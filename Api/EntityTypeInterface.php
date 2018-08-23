<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Api;

interface EntityTypeInterface
{
    public function getCode(): string;

    public function getDescription(): string;
}