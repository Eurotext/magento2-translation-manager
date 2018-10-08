<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Validator\ProjectStatusValidatorInterface;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
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

    /**
     * @var ProjectStatusValidatorInterface
     */
    private $projectStatusValidator;

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    public function __construct(
        ReceiveProjectService $receiveProject,
        ProjectRepositoryInterface $projectRepository,
        ProjectStatusValidatorInterface $projectStatusValidator,
        ProjectStateMachine $projectStateMachine,
        PushConsoleLogHandler $pushConsoleLog,
        AppState $appState
    ) {
        parent::__construct();

        $this->receiveProject         = $receiveProject;
        $this->projectRepository      = $projectRepository;
        $this->projectStatusValidator = $projectStatusValidator;
        $this->projectStateMachine    = $projectStateMachine;
        $this->pushConsoleLog         = $pushConsoleLog;
        $this->appState               = $appState;
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

        // Load Project
        $project = $this->projectRepository->getById($projectId);

        // Push the ConsoleLogger to the EurotextLogger so we directly see console output
        $this->pushConsoleLog->push($output);

        // check API Project Status === finished
        $isFinished = $this->projectStatusValidator->validate($project, ProjectStatusEnum::FINISHED());
        if (!$isFinished) {
            $output->writeln('Eurotext Translation Project is not marked as finished');

            return;
        }

        // Set status ACCEPTED, because services are checking for the correct workflow
        $this->projectStateMachine->applyById($projectId, ProjectInterface::STATUS_TRANSLATED);
        $this->projectStateMachine->applyById($projectId, ProjectInterface::STATUS_ACCEPTED);

        $this->receiveProject->executeById($projectId);
    }
} 
