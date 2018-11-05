<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command;

use Eurotext\TranslationManager\Command\CheckProjectStatusCronCommand;
use Eurotext\TranslationManager\Cron\CheckProjectStatusCron;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class CheckProjectStatusCronUnitTest extends UnitTestAbstract
{
    /** @var CheckProjectStatusCron */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    /** @var CheckProjectStatusCron|\PHPUnit_Framework_MockObject_MockObject */
    protected $checkProjectStatus;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->checkProjectStatus =
            $this->getMockBuilder(CheckProjectStatusCron::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['execute'])
                 ->getMock();

        $this->sut = $this->objectManager->getObject(
            CheckProjectStatusCronCommand::class,
            ['checkProjectStatus' => $this->checkProjectStatus]
        );
    }

    public function testItShouldUseCheckProjectStatus()
    {
        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $outputFormatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
        $output->method('getFormatter')->willReturn($outputFormatter);

        $this->checkProjectStatus->expects($this->once())->method('execute');

        $this->sut->run($input, $output);
    }

}
