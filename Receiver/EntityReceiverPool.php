<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Receiver;

use Eurotext\TranslationManager\Api\EntityReceiverInterface;

class EntityReceiverPool
{
    /**
     * @var EntityReceiverInterface[]
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return EntityReceiverInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
