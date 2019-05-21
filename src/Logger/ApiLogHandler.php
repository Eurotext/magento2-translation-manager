<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Logger;

use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Logger\Handler\Base as BaseLogger;

class ApiLogHandler extends BaseLogger
{
    const LOG_FILENAME = '/var/log/eurotext_api.log';

    public function __construct(FileDriver $filesystem, string $filePath = null)
    {
        parent::__construct($filesystem, $filePath, self::LOG_FILENAME);
    }
}