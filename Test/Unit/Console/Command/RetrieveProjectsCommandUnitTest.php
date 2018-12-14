<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Console\Command;

use Eurotext\TranslationManager\Console\Command\RetrieveProjectsCommand;
use Eurotext\TranslationManager\Cron\RetrieveProjectsCron;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class RetrieveProjectsCommandUnitTest extends UnitTestAbstract
{
    /** @var RetrieveProjectsCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    /** @var RetrieveProjectsCron|\PHPUnit_Framework_MockObject_MockObject */
    protected $retrieveProjectsCron;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->retrieveProjectsCron = $this->createMock(RetrieveProjectsCron::class);

        $this->sut = $this->objectManager->getObject(
            RetrieveProjectsCommand::class,
            [
                'retrieveProjectsCron' => $this->retrieveProjectsCron,
            ]
        );
    }

    public function testItShouldUseCheckProjectStatusServiceExecuteByIdWithProjectId()
    {
        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $outputFormatter = $this->createMock(OutputFormatterInterface::class);
        $output->method('getFormatter')->willReturn($outputFormatter);

        $this->retrieveProjectsCron->expects($this->once())->method('execute');

        $this->sut->run($input, $output);
    }

}
