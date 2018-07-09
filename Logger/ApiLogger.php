<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Logger;

use Monolog\Logger;

class ApiLogger extends Logger
{
    public function __construct(ApiLogHandler $apiLogHandler)
    {
        $handlers = ['api_log_handler' => $apiLogHandler];

        parent::__construct('eurotext_api_logger', $handlers);
    }

}