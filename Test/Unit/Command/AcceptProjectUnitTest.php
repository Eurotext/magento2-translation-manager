<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command;

use Eurotext\TranslationManager\Command\AcceptProjectCommand;
use Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class AcceptProjectUnitTest extends UnitTestAbstract
{
    /** @var AcceptProjectCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    /** @var ProjectStateMachine|\PHPUnit_Framework_MockObject_MockObject */
    protected $projectStateMachine;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->projectStateMachine =
            $this->getMockBuilder(ProjectStateMachine::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['applyById'])
                 ->getMock();

        $this->sut = $this->objectManager->getObject(
            AcceptProjectCommand::class,
            [
                'projectStateMachine' => $this->projectStateMachine,
            ]
        );
    }

    public function testItShouldSetStatusToAccepted()
    {
        $projectId = 1;

        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $outputFormatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
        $output->method('getFormatter')->willReturn($outputFormatter);
        $output->expects($this->once())->method('writeln');

        $input->expects($this->once())->method('getArgument')->willReturn($projectId);

        $this->projectStateMachine->expects($this->once())->method('applyById')->with($projectId);

        $this->sut->run($input, $output);
    }

    public function testItShouldThrowExceptionIfStatusChangeIsNotAllowed()
    {
        $this->expectException(IllegalProjectStatusChangeException::class);

        $projectId = 1;

        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $outputFormatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
        $output->method('getFormatter')->willReturn($outputFormatter);
        $output->expects($this->never())->method('writeln');

        $input->expects($this->once())->method('getArgument')->willReturn($projectId);

        $this->projectStateMachine->expects($this->once())->method('applyById')->with($projectId)
                                  ->willThrowException(new IllegalProjectStatusChangeException());

        $this->sut->run($input, $output);
    }

}
