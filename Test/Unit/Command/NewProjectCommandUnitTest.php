<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command;

use Eurotext\TranslationManager\Console\Command\NewProjectCommand;
use Eurotext\TranslationManager\Console\Service\NewProjectService;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use PHPUnit\Framework\TestCase;

class NewProjectCommandUnitTest extends TestCase
{
    /** @var \Eurotext\TranslationManager\Console\Service\NewProjectService|\PHPUnit_Framework_MockObject_MockObject */
    protected $newProjectService;

    /** @var NewProjectCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->newProjectService =
            $this->getMockBuilder(NewProjectService::class)
                 ->setMethods(['execute'])
                 ->disableOriginalConstructor()
                 ->getMock();

        $this->sut = new NewProjectCommand($this->newProjectService);
    }

    /**
     * @throws \Exception
     *
     * @test
     */
    public function itShouldExecuteTheCreateProjectService()
    {
        $this->newProjectService->expects($this->once())->method('execute');

        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $this->sut->run($input, $output);
    }

}
