<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Entity;

class EntityDataSaverPool
{
    /**
     * @var \Eurotext\TranslationManager\Api\EntityDataSaverInterface[]
     */
    private $items;

    /**
     * @param \Eurotext\TranslationManager\Api\EntityDataSaverInterface[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Eurotext\TranslationManager\Api\EntityDataSaverInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
