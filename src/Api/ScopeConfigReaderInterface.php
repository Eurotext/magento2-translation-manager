<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Api;

interface ScopeConfigReaderInterface
{
    public function getLocaleForStore(int $scopeCode): string;
}