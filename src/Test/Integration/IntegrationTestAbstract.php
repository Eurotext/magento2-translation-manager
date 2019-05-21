<?php
declare(strict_types=1);
/**
 * @copyright see LICENSE.txt
 *
 * @see LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestAbstract extends TestCase
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $objectManager;

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
    }
}
