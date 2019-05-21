<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Entity;

class EntityTypePool
{
    /**
     * @var \Eurotext\TranslationManager\Api\EntityTypeInterface[]
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Eurotext\TranslationManager\Api\EntityTypeInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
