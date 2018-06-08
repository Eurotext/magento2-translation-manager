<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Seeder;

/**
 * ProjectSeederPool
 */
class EntitySeederPool
{
    /**
     * @var \Eurotext\TranslationManager\Api\EntitySeederInterface[]
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Eurotext\TranslationManager\Api\EntitySeederInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}