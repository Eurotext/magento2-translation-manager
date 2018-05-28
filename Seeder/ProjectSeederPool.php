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
class ProjectSeederPool
{
    /**
     * @var \Eurotext\TranslationManager\Api\ProjectSeederInterface[]
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Eurotext\TranslationManager\Api\ProjectSeederInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}