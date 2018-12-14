<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Console\Command;

use Eurotext\TranslationManager\Console\Command\SendProjectCommand;
use Eurotext\TranslationManager\Logger\PushConsoleLogHandler;
use Eurotext\TranslationManager\Service\SendProjectServiceInterface;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class SendProjectCommandUnitTest extends UnitTestAbstract
{
    /** @var SendProjectServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $sendProjectService;

    /** @var SendProjectCommand */
    protected $sut;

    /** @var ConsoleMockBuilder */
    protected $builder;

    /** @var State */
    protected $appState;

    protected $pushConsoleLog;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConsoleMockBuilder($this);

        $this->sendProjectService = $this->createMock(SendProjectServiceInterface::class);
        $this->pushConsoleLog     = $this->createMock(PushConsoleLogHandler::class);
        $this->appState           = $this->createMock(State::class);

        $this->sut = $this->objectManager->getObject(
            SendProjectCommand::class, [
                'sendProject'    => $this->sendProjectService,
                'pushConsoleLog' => $this->pushConsoleLog,
                'appState'       => $this->appState,
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
        $projectId = 1;

        $this->sendProjectService->expects($this->once())->method('executeById')
                                 ->with($projectId)
                                 ->willReturn(['project' => 1]);

        $input = $this->builder->buildConsoleInputMock();
        $input->expects($this->once())->method('getArgument')->willReturn($projectId);

        $output = $this->builder->buildConsoleOutputMock();

        $this->sut->run($input, $output);
    }

    public function testItShouldNotStopForLocalicedExceptions()
    {
        $projectId = 1;

        $exception = new LocalizedException(new \Magento\Framework\Phrase('some error'));
        $this->appState->expects($this->once())->method('setAreaCode')->with('adminhtml')
                       ->willThrowException($exception);

        $this->sendProjectService->expects($this->once())->method('executeById')
                                 ->with($projectId)->willReturn(['project' => 1]);

        $input = $this->builder->buildConsoleInputMock();
        $input->expects($this->once())->method('getArgument')->willReturn($projectId);

        $output = $this->builder->buildConsoleOutputMock();

        $this->sut->run($input, $output);
    }

}
