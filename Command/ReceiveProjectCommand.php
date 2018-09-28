<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Logger\PushConsoleLogHandler;
use Eurotext\TranslationManager\Service\ReceiveProjectService;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReceiveProjectCommand extends Command
{
    const ARG_ID              = 'id';
    const COMMAND_NAME        = 'etm:project:receive';
    const COMMAND_DESCRIPTION = 'Receive Project Translations from ETM2';

    /**
     * @var ReceiveProjectService
     */
    private $receiveProject;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var PushConsoleLogHandler
     */
    private $pushConsoleLog;

    /**
     * @var ProjectStateMachine
     */
    private $projectStateMachine;

    public function __construct(
        ReceiveProjectService $receiveProject,
        ProjectStateMachine $projectStateMachine,
        PushConsoleLogHandler $pushConsoleLog,
        AppState $appState
    ) {
        parent::__construct();

        $this->receiveProject = $receiveProject;
        $this->pushConsoleLog = $pushConsoleLog;
        $this->appState       = $appState;
        $this->projectStateMachine = $projectStateMachine;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        $this->addArgument(self::ARG_ID, InputArgument::REQUIRED);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = (int)$input->getArgument(self::ARG_ID);

        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (LocalizedException $e) {
            // the area code is already set
        }

        // Push the ConsoleLogger to the EurotextLogger so we directly see console output
        $this->pushConsoleLog->push($output);

        // @todo Service: check API Project Status === finished

        // Set status ACCEPTED, because services are checking for the correct workflow
        $this->projectStateMachine->applyById($projectId, ProjectInterface::STATUS_TRANSLATED);
        $this->projectStateMachine->applyById($projectId, ProjectInterface::STATUS_ACCEPTED);

        $this->receiveProject->executeById($projectId);
    }
} 
