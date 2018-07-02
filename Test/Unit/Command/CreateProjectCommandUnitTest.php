<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command;

use Eurotext\TranslationManager\Command\NewProjectCommand;
use Eurotext\TranslationManager\Command\Service\NewProjectService;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use PHPUnit\Framework\TestCase;

class CreateProjectCommandUnitTest extends TestCase
{
    /** @var \Eurotext\TranslationManager\Command\Service\NewProjectService|\PHPUnit\Framework\MockObject\MockObject */
    protected $createProjectService;

    /** @var NewProjectCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder  = new ConsoleMockBuilder($this);

        $this->createProjectService = $this->getMockBuilder(NewProjectService::class)
            ->setMethods(['execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new NewProjectCommand($this->createProjectService);
    }

    /**
     * @throws \Exception
     *
     * @test
     */
    public function itShouldExecuteTheCreateProjectService()
    {
        $this->createProjectService->expects($this->once())->method('execute');

        $input = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $this->sut->run($input, $output);
    }

}
