<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class UnitTestAbstract extends TestCase
{
    /** @var ObjectManager */
    protected $objectManager;

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = new ObjectManager($this);
    }
}