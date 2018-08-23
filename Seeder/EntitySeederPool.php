<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Seeder;

use Eurotext\TranslationManager\Api\EntitySeederInterface;

/**
 * ProjectSeederPool
 */
class EntitySeederPool
{
    /**
     * @var EntitySeederInterface[]
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return EntitySeederInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param $code
     *
     * @return EntitySeederInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getByCode($code): EntitySeederInterface
    {
        if (!array_key_exists($code, $this->items)) {
            $msg = sprintf('unknown seeder with code: %s', $code);
            throw new \InvalidArgumentException($msg);
        }

        return $this->items[$code];
    }
}
