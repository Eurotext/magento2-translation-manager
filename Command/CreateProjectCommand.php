<?php

namespace Eurotext\TranslationManager\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProjectCommand extends Command
{
    protected function configure()
    {
        $this->setName('etm:create-project');
        $this->setDescription('Create Project for ETM2');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
} 