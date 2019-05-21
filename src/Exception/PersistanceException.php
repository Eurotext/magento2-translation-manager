<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Exception;

use Throwable;

class PersistanceException extends \Exception
{
    /**
     * @var int
     */
    private $entityId;

    public function __construct(int $entityId = 0, string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->entityId = $entityId;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

}