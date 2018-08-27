<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Service\SendProjectService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendProjectCommand extends Command
{
    const ARG_ID = 'id';

    /**
     * @var SendProjectService
     */
    private $sendProject;

    public function __construct(SendProjectService $sendProject)
    {
        parent::__construct();

        $this->sendProject = $sendProject;
    }

    protected function configure()
    {
        $this->setName('etm:project:send');
        $this->setDescription('Send Project to ETM2');

        $this->addArgument(self::ARG_ID, InputArgument::REQUIRED);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = (int)$input->getArgument(self::ARG_ID);

        $result = $this->sendProject->executeById($projectId);

        foreach ($result as $typeKey => $transferStatus) {
            $status = $transferStatus === 1 ? 'success' : $transferStatus;
            $output->writeln(sprintf('Create %s: %s', $typeKey, $status));
        }

    }
} 
