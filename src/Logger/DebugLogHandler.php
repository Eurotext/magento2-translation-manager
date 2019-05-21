<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Logger;

use Magento\Framework\Logger\Handler\Base as BaseLogger;

class DebugLogHandler extends BaseLogger
{
    protected $fileName = '/var/log/eurotext_debug.log';
}