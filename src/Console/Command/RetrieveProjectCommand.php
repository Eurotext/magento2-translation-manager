<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Console\Command;

use Eurotext\TranslationManager\Console\Service\RetrieveProjectCliService;
use Eurotext\TranslationManager\Logger\PushConsoleLogHandler;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RetrieveProjectCommand extends Command
{
    const ARG_ID              = 'id';
    const COMMAND_NAME        = 'etm:project:retrieve';
    const COMMAND_DESCRIPTION = 'Retrieve Project Translations from Eurotext';

    /**
     * @var RetrieveProjectCliService
     */
    private $retrieveProject;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var PushConsoleLogHandler
     */
    private $pushConsoleLog;

    public function __construct(
        RetrieveProjectCliService $retrieveProject,
        PushConsoleLogHandler $pushConsoleLog,
        AppState $appState
    ) {
        parent::__construct();

        $this->retrieveProject = $retrieveProject;
        $this->pushConsoleLog  = $pushConsoleLog;
        $this->appState        = $appState;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        $this->addArgument(self::ARG_ID, InputArgument::REQUIRED, 'the project id');

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

        $this->retrieveProject->executeById($projectId);
    }
} 
