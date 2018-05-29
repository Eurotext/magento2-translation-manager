<?php
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Console\Output\OutputInterface
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
     * @return \Symfony\Component\Console\Input\InputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function buildConsoleInputMock()
    {
        $input = $this->getMockBuilder(\Symfony\Component\Console\Input\InputInterface::class)
            ->setMethods(['getArgument'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        return $input;
    }

    protected function getMockBuilder($className): \PHPUnit_Framework_MockObject_MockBuilder
    {
        return new \PHPUnit_Framework_MockObject_MockBuilder($this->testCase, $className);
    }

}