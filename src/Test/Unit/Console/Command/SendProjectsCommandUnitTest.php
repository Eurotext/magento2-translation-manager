<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Console\Command;

use Eurotext\TranslationManager\Console\Command\CheckStatusCommand;
use Eurotext\TranslationManager\Console\Command\SendProjectsCommand;
use Eurotext\TranslationManager\Cron\SendProjectsCron;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class SendProjectsCommandUnitTest extends UnitTestAbstract
{
    /** @var CheckStatusCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    /** @var SendProjectsCron|\PHPUnit_Framework_MockObject_MockObject */
    protected $sendProjectsCron;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->sendProjectsCron = $this->createMock(SendProjectsCron::class);

        $this->sut = $this->objectManager->getObject(
            SendProjectsCommand::class,
            [
                'sendProjectsCron' => $this->sendProjectsCron,
            ]
        );
    }

    public function testItShouldUseCheckProjectStatusServiceExecuteByIdWithProjectId()
    {
        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $outputFormatter = $this->createMock(OutputFormatterInterface::class);
        $output->method('getFormatter')->willReturn($outputFormatter);

        $this->sendProjectsCron->expects($this->once())->method('execute');

        $this->sut->run($input, $output);
    }

}
