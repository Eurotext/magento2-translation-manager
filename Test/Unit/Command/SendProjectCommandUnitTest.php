<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command;

use Eurotext\TranslationManager\Command\SendProjectCommand;
use Eurotext\TranslationManager\Service\SendProjectService;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use PHPUnit\Framework\TestCase;

class SendProjectCommandUnitTest extends TestCase
{
    /** @var \Eurotext\TranslationManager\Service\SendProjectService|\PHPUnit_Framework_MockObject_MockObject */
    protected $sendProjectService;

    /** @var SendProjectCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->sendProjectService =
            $this->getMockBuilder(SendProjectService::class)
                 ->setMethods(['executeById'])
                 ->disableOriginalConstructor()
                 ->getMock();

        $this->sut = new SendProjectCommand($this->sendProjectService);
    }

    /**
     * @throws \Exception
     *
     * @test
     */
    public function itShouldExecuteTheCreateProjectService()
    {
        $projectId = 1;

        $this->sendProjectService->expects($this->once())->method('executeById')->with($projectId);

        $input = $this->builder->buildConsoleInputMock();
        $input->expects($this->once())->method('getArgument')->willReturn($projectId);

        $output = $this->builder->buildConsoleOutputMock();

        $this->sut->run($input, $output);
    }

}
