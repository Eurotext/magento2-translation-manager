<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command;

use Eurotext\TranslationManager\Command\CreateProjectCommand;
use Eurotext\TranslationManager\Command\Service\CreateProjectService;
use PHPUnit\Framework\TestCase;

class CreateProjectCommandUnitTest extends TestCase
{
    /** @var \Eurotext\TranslationManager\Command\Service\CreateProjectService|\PHPUnit\Framework\MockObject\MockObject */
    protected $createProjectService;

    /** @var CreateProjectCommand */
    protected $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->createProjectService = $this->getMockBuilder(CreateProjectService::class)
            ->setMethods(['execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new CreateProjectCommand($this->createProjectService);
    }

    /**
     * @throws \Exception
     *
     * @test
     */
    public function itShouldExecuteTheCreateProjectService()
    {
        $this->createProjectService->expects($this->once())->method('execute');

        $input = $this->buildConsoleInputMock();
        $output = $this->buildConsoleOutputMock();

        $this->sut->run($input, $output);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function buildConsoleOutputMock()
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
    protected function buildConsoleInputMock()
    {
        $input = $this->getMockBuilder(\Symfony\Component\Console\Input\InputInterface::class)
            ->setMethods(['getArgument'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        return $input;
    }

}