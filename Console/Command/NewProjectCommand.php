<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Console\Command;

use Eurotext\TranslationManager\Console\Service\NewProjectService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewProjectCommand extends Command
{
    const COMMAND_NAME        = 'etm:project:new';
    const COMMAND_DESCRIPTION = 'Create new Project';

    /**
     * @var NewProjectService
     */
    private $newProject;

    public function __construct(NewProjectService $newProject)
    {
        parent::__construct();

        $this->newProject = $newProject;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        $this->addArgument(
            NewProjectService::ARG_NAME, InputArgument::REQUIRED,
            'the project name'
        );
        $this->addArgument(
            NewProjectService::ARG_STORE_ID_SRC, InputArgument::REQUIRED,
            'the store-view id that is used as source'
        );
        $this->addArgument(
            NewProjectService::ARG_STORE_ID_DEST, InputArgument::REQUIRED,
            'the store-view id that the translation is for'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->newProject->execute($input, $output);
    }
} 
