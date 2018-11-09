<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Console\Command;

use Eurotext\TranslationManager\Cron\CheckProjectStatusCron;
use Eurotext\TranslationManager\Logger\PushConsoleLogHandler;
use Eurotext\TranslationManager\Service\Project\CheckProjectStatusServiceInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckStatusCommand extends Command
{
    const ARG_ID              = 'id';
    const COMMAND_NAME        = 'etm:project:status-check';
    const COMMAND_DESCRIPTION = 'Check Project Status at Eurotext. Will update project status in Magento.';

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var PushConsoleLogHandler
     */
    private $pushConsoleLog;

    /**
     * @var CheckProjectStatusServiceInterface
     */
    private $checkProjectStatus;

    /**
     * @var CheckProjectStatusCron
     */
    private $checkProjectStatusCron;

    public function __construct(
        CheckProjectStatusServiceInterface $checkProjectStatus,
        CheckProjectStatusCron $checkProjectStatusCron,
        PushConsoleLogHandler $pushConsoleLog,
        AppState $appState
    ) {
        parent::__construct();

        $this->checkProjectStatus     = $checkProjectStatus;
        $this->checkProjectStatusCron = $checkProjectStatusCron;
        $this->pushConsoleLog         = $pushConsoleLog;
        $this->appState               = $appState;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        $this->addArgument(self::ARG_ID, InputArgument::OPTIONAL);

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

        if ($projectId > 0) {
            $this->checkProjectStatus->executeById($projectId);
        } else {
            $this->checkProjectStatusCron->execute();
        }
    }
} 
