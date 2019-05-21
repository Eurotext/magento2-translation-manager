<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Logger;

use Monolog\Logger;

class DebugLogger extends Logger
{
    public function __construct(DebugLogHandler $apiLogHandler)
    {
        $handlers = ['debug_log_handler' => $apiLogHandler];

        parent::__construct('eurotext_debug_logger', $handlers);
    }

}