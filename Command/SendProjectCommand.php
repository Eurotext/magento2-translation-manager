<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Logger\PushConsoleLogHandler;
use Eurotext\TranslationManager\Service\SendProjectService;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendProjectCommand extends Command
{
    const ARG_ID              = 'id';
    const COMMAND_NAME        = 'etm:project:send';
    const COMMAND_DESCRIPTION = 'Send Project to ETM2';

    /**
     * @var SendProjectService
     */
    private $sendProject;

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
        SendProjectService $sendProject,
        ProjectStateMachine $projectStateMachine,
        PushConsoleLogHandler $pushConsoleLog,
        AppState $appState
    ) {
        parent::__construct();

        $this->sendProject         = $sendProject;
        $this->projectStateMachine = $projectStateMachine;
        $this->pushConsoleLog      = $pushConsoleLog;
        $this->appState            = $appState;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        $this->addArgument(self::ARG_ID, InputArgument::REQUIRED);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Eurotext\TranslationManager\Exception\IllegalProjectStatusChangeException
     * @throws \Eurotext\TranslationManager\Exception\InvalidProjectStatusException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (LocalizedException $e) {
            // the area code is already set
        }

        // Push the ConsoleLogger to the EurotextLogger so we directly see console output
        $this->pushConsoleLog->push($output);

        $projectId = (int)$input->getArgument(self::ARG_ID);

        // Set status Transfer, because services are checking for the correct workflow
        $this->projectStateMachine->applyById($projectId, ProjectInterface::STATUS_TRANSFER);

        $this->sendProject->executeById($projectId);
    }

} 
