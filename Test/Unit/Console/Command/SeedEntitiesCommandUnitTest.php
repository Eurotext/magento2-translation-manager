<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Console\Command;

use Eurotext\TranslationManager\Console\Command\SeedEntitiesCommand;
use Eurotext\TranslationManager\Console\Service\SeedEntitiesService;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class SeedEntitiesCommandUnitTest extends UnitTestAbstract
{
    /** @var SeedEntitiesService|\PHPUnit_Framework_MockObject_MockObject */
    protected $seedEntitiesService;

    /** @var SeedEntitiesCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    /** @var State|\PHPUnit_Framework_MockObject_MockObject */
    protected $appState;

    protected $pushConsoleLog;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->seedEntitiesService = $this->createMock(SeedEntitiesService::class);
        $this->appState            = $this->createMock(State::class);

        $this->sut = $this->objectManager->getObject(
            SeedEntitiesCommand::class, [
                'seedEntitiesService' => $this->seedEntitiesService,
                'appState'            => $this->appState,
            ]
        );
    }

    /**
     * @throws \Exception
     *
     * @test
     */
    public function itShouldExecuteTheCreateProjectService()
    {
        $this->seedEntitiesService->expects($this->once())->method('execute');

        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $this->sut->run($input, $output);
    }

    public function testItShouldNotStopForLocalicedExceptions()
    {
        $exception = new LocalizedException(new \Magento\Framework\Phrase('some error'));
        $this->appState->expects($this->once())->method('setAreaCode')->with('adminhtml')
                       ->willThrowException($exception);

        $input  = $this->builder->buildConsoleInputMock();
        $output = $this->builder->buildConsoleOutputMock();

        $this->seedEntitiesService->expects($this->once())->method('execute')->with($input, $output);

        $this->sut->run($input, $output);
    }

}
