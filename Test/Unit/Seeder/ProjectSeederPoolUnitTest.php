<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Seeder;

use Eurotext\TranslationManager\Api\EntitySeederInterface;
use Eurotext\TranslationManager\Seeder\EntitySeederPool;
use PHPUnit\Framework\TestCase;

class ProjectSeederPoolUnitTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnAnArrayOfSeeders()
    {
        $seederMock = $this->createMock(EntitySeederInterface::class);

        $sut = new EntitySeederPool([$seederMock]);

        $items = $sut->getItems();

        $item = array_shift($items);

        $this->assertInstanceOf(EntitySeederInterface::class, $item);
    }

}
