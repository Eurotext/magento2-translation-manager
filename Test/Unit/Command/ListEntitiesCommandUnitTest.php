<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Command;

use Eurotext\TranslationManager\Api\EntityTypeInterface;
use Eurotext\TranslationManager\Command\ListEntitiesCommand;
use Eurotext\TranslationManager\Command\NewProjectCommand;
use Eurotext\TranslationManager\Entity\EntityTypePool;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class ListEntitiesCommandUnitTest extends TestCase
{
    /** @var NewProjectCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $entityType = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entityType->expects($this->once())->method('getCode')->willReturn('some-code');
        $entityType->expects($this->once())->method('getDescription')->willReturn('some-desc');

        $entityTypePool = new EntityTypePool(['typekey' => $entityType]);

        $this->sut = new ListEntitiesCommand($entityTypePool);
    }

    /**
     * @throws \Exception
     *
     * @test
     */
    public function itShouldExecuteTheCreateProjectService()
    {
        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $outputFormatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
        $output->method('getFormatter')->willReturn($outputFormatter);

        $this->sut->run($input, $output);
    }

}
