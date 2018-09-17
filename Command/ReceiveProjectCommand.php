<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Service\ReceiveProjectService;
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

    public function __construct(ReceiveProjectService $receiveProject, AppState $appState)
    {
        parent::__construct();

        $this->receiveProject = $receiveProject;
        $this->appState       = $appState;
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

        $result = $this->receiveProject->executeById($projectId);

        foreach ($result as $typeKey => $transferStatus) {
            $status = $transferStatus === 1 ? 'success' : $transferStatus;
            $output->writeln(sprintf('Receive %s: %s', $typeKey, $status));
        }
    }
} 
