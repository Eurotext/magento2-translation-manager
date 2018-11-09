<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Console\Command;

use Eurotext\TranslationManager\Console\Command\ReceiveProjectsCommand;
use Eurotext\TranslationManager\Cron\ReceiveProjectsCron;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class ReceiveProjectsCommandUnitTest extends UnitTestAbstract
{
    /** @var ReceiveProjectsCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    /** @var ReceiveProjectsCron|\PHPUnit_Framework_MockObject_MockObject */
    protected $receiveProjectsCron;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->receiveProjectsCron =
            $this->getMockBuilder(ReceiveProjectsCron::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['execute'])
                 ->getMock();

        $this->sut = $this->objectManager->getObject(
            ReceiveProjectsCommand::class,
            [
                'receiveProjectsCron' => $this->receiveProjectsCron,
            ]
        );
    }

    public function testItShouldUseCheckProjectStatusServiceExecuteByIdWithProjectId()
    {
        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $outputFormatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
        $output->method('getFormatter')->willReturn($outputFormatter);

        $this->receiveProjectsCron->expects($this->once())->method('execute');

        $this->sut->run($input, $output);
    }

}
