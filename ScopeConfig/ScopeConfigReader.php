<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\ScopeConfig;

use Eurotext\TranslationManager\Api\ScopeConfigReaderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ScopeConfigReader implements ScopeConfigReaderInterface
{
    const CONFIG_PATH_LOCALE_CODE = 'general/locale/code';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getLocaleForStore(int $scopeCode): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_LOCALE_CODE, 'stores', $scopeCode);
    }
}