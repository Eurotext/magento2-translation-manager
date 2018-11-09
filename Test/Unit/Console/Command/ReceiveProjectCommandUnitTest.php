<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Console\Command;

use Eurotext\TranslationManager\Console\Command\ReceiveProjectCommand;
use Eurotext\TranslationManager\Console\Service\ReceiveProjectCliService;
use Eurotext\TranslationManager\Logger\PushConsoleLogHandler;
use Eurotext\TranslationManager\Test\Builder\ConsoleMockBuilder;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class ReceiveProjectCommandUnitTest extends UnitTestAbstract
{
    /** @var \Eurotext\TranslationManager\Service\SendProjectService|\PHPUnit_Framework_MockObject_MockObject */
    protected $receiveProject;

    /** @var ReceiveProjectCommand */
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

        $this->receiveProject =
            $this->getMockBuilder(ReceiveProjectCliService::class)
                 ->setMethods(['executeById'])
                 ->disableOriginalConstructor()
                 ->getMock();

        $this->pushConsoleLog =
            $this->getMockBuilder(PushConsoleLogHandler::class)
                 ->disableOriginalConstructor()
                 ->getMock();

        $this->appState = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();

        $this->sut = $this->objectManager->getObject(
            ReceiveProjectCommand::class, [
                'receiveProject' => $this->receiveProject,
                'pushConsoleLog' => $this->pushConsoleLog,
                'appState'       => $this->appState,
            ]
        );
    }

    public function testItShouldExecuteTheCreateProjectService()
    {
        $projectId = 1;

        $this->receiveProject->expects($this->once())->method('executeById')
                             ->with($projectId)->willReturn(['project' => 1]);

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

        $this->receiveProject->expects($this->once())->method('executeById')
                             ->with($projectId)->willReturn(['project' => 1]);

        $input = $this->builder->buildConsoleInputMock();
        $input->expects($this->once())->method('getArgument')->willReturn($projectId);

        $output = $this->builder->buildConsoleOutputMock();

        $this->sut->run($input, $output);
    }

}
