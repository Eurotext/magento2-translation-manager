<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Command\Service\NewProjectService\Proxy as NewProjectService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewProjectCommand extends Command
{
    /**
     * @var NewProjectService
     */
    private $createProject;

    public function __construct(NewProjectService $sendProject)
    {
        parent::__construct();

        $this->createProject = $sendProject;
    }

    protected function configure()
    {
        $this->setName('etm:project:new');
        $this->setDescription('Create Project for ETM2');

        $this->addArgument(NewProjectService::ARG_NAME, InputArgument::REQUIRED);
        $this->addArgument(NewProjectService::ARG_STORE_ID_SRC, InputArgument::REQUIRED);
        $this->addArgument(NewProjectService::ARG_STORE_ID_DEST, InputArgument::REQUIRED);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createProject->execute($input, $output);
    }
} 
