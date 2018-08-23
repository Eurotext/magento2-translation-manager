<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Entity;

use Eurotext\TranslationManager\Api\EntityTypeInterface;

class ProductEntityType implements EntityTypeInterface
{
    const CODE        = 'product';
    const DESCRIPTION = 'Product';

    public function getCode(): string
    {
        return self::CODE;
    }

    public function getDescription(): string
    {
        return (string)__(self::DESCRIPTION);
    }
}