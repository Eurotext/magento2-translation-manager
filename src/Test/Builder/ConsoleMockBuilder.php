<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Builder;

use PHPUnit\Framework\TestCase;

class ConsoleMockBuilder
{
    /**
     * @var \PHPUnit\Framework\TestCase
     */
    private $testCase;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function buildConsoleOutputMock()
    {
        /** @var \Symfony\Component\Console\Output\OutputInterface|\PHPUnit\Framework\MockObject\MockObject $output */
        $output = $this->getMockBuilder(\Symfony\Component\Console\Output\OutputInterface::class)
            ->setMethods(['writeln'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        return $output;
    }

    /**
     * @return \Symfony\Component\Console\Input\InputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function buildConsoleInputMock()
    {
        $input = $this->getMockBuilder(\Symfony\Component\Console\Input\InputInterface::class)
            ->setMethods(['getArgument'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        return $input;
    }

    protected function getMockBuilder($className): \PHPUnit\Framework\MockObject\MockBuilder
    {
        return new \PHPUnit\Framework\MockObject\MockBuilder($this->testCase, $className);
    }

}
