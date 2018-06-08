<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Sender;

class EntitySenderPool
{
    /**
     * @var \Eurotext\TranslationManager\Api\EntitySenderInterface[]
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Eurotext\TranslationManager\Api\EntitySenderInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}