<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Seeder;

use Eurotext\TranslationManager\Api\ProjectSeederInterface;
use Eurotext\TranslationManager\Seeder\ProjectSeederPool;
use PHPUnit\Framework\TestCase;

class ProjectSeederPoolUnitTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnAnArrayOfSeeders()
    {
        $seederMock = $this->getMockBuilder(ProjectSeederInterface::class)
            ->setMethods(['seed'])
            ->getMockForAbstractClass();

        $sut = new ProjectSeederPool([$seederMock]);

        $items = $sut->getItems();

        $item = array_shift($items);

        $this->assertInstanceOf(ProjectSeederInterface::class, $item);
    }

}