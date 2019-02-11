<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Ui\EntityDataLoader;

class EntityDataLoaderPool
{
    /**
     * @var \Eurotext\TranslationManager\Api\EntityDataLoaderInterface[]
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Eurotext\TranslationManager\Api\EntityDataLoaderInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
