<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Retriever;

use Eurotext\TranslationManager\Api\EntityRetrieverInterface;

class EntityRetrieverPool
{
    /**
     * @var EntityRetrieverInterface[]
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return EntityRetrieverInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
