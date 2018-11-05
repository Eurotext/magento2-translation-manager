<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command;

use Eurotext\TranslationManager\Api\EntityTypeInterface;
use Eurotext\TranslationManager\Command\CheckProjectStatusCommand;
use Eurotext\TranslationManager\Service\Project\CheckProjectStatusServiceInterface;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class CheckProjectStatusUnitTest extends UnitTestAbstract
{
    /** @var CheckProjectStatusCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    /** @var CheckProjectStatusCommand|\PHPUnit_Framework_MockObject_MockObject */
    protected $checkProjectStatus;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->checkProjectStatus =
            $this->getMockBuilder(CheckProjectStatusServiceInterface::class)
                 ->setMethods(['executeById'])
                 ->getMockForAbstractClass();

        $this->sut = $this->objectManager->getObject(
            CheckProjectStatusCommand::class,
            ['checkProjectStatus' => $this->checkProjectStatus]
        );
    }

    public function testItShouldUseCheckProjectStatusServiceExecuteByIdWithProjectId()
    {
        $projectId = 1;

        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $outputFormatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
        $output->method('getFormatter')->willReturn($outputFormatter);

        $input->expects($this->once())->method('getArgument')->willReturn($projectId);

        $this->checkProjectStatus->expects($this->once())->method('executeById')->with($projectId);

        $this->sut->run($input, $output);
    }

}
