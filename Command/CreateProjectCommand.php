<?php

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Command\Service\CreateProjectService\Proxy as CreateProjectService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProjectCommand extends Command
{
    /**
     * @var \Eurotext\TranslationManager\Command\Service\CreateProjectService
     */
    private $createProject;

    public function __construct(CreateProjectService $createProject)
    {
        parent::__construct();

        $this->createProject = $createProject;
    }

    protected function configure()
    {
        $this->setName('etm:create-project');
        $this->setDescription('Create Project for ETM2');

        $this->addArgument(CreateProjectService::ARG_NAME, InputArgument::REQUIRED);
        $this->addArgument(CreateProjectService::ARG_STORE_ID_SRC, InputArgument::REQUIRED);
        $this->addArgument(CreateProjectService::ARG_STORE_ID_DEST, InputArgument::REQUIRED);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createProject->execute($input, $output);
    }
} 