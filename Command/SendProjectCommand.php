<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Command\Service\SendProjectService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendProjectCommand extends Command
{
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

        $this->addArgument(SendProjectService::ARG_ID, InputArgument::REQUIRED);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sendProject->execute($input, $output);
    }
} 
