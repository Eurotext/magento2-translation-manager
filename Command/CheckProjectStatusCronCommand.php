<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Cron\CheckProjectStatusCron;
use Eurotext\TranslationManager\Logger\PushConsoleLogHandler;
use Eurotext\TranslationManager\Service\Project\CheckProjectStatusService;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckProjectStatusCronCommand extends Command
{
    const ARG_ID              = 'id';
    const COMMAND_NAME        = 'etm:project:check-status-cron';
    const COMMAND_DESCRIPTION = 'Check Project Status against ETM2 API';

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var PushConsoleLogHandler
     */
    private $pushConsoleLog;

    /**
     * @var CheckProjectStatusService
     */
    private $checkProjectStatus;

    public function __construct(
        CheckProjectStatusCron $checkProjectStatus,
        PushConsoleLogHandler $pushConsoleLog,
        AppState $appState
    ) {
        parent::__construct();

        $this->checkProjectStatus = $checkProjectStatus;
        $this->pushConsoleLog     = $pushConsoleLog;
        $this->appState           = $appState;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

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

        $this->checkProjectStatus->execute();
    }
} 
