<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Command\Service\SeedEntitiesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedEntitiesCommand extends Command
{
    const NAME        = 'etm:entity:seed';
    const DESCRIPTION = 'Seed entites for a given project';

    /**
     * @var SeedEntitiesService
     */
    private $seedEntitiesService;

    public function __construct(SeedEntitiesService $seedEntitiesService)
    {
        parent::__construct();
        $this->seedEntitiesService = $seedEntitiesService;
    }

    protected function configure()
    {
        $this->setName(self::NAME);
        $this->setDescription(self::DESCRIPTION);

        $this->addArgument(SeedEntitiesService::ARG_PROJECT_ID, InputArgument::REQUIRED);
        $this->addArgument(
            SeedEntitiesService::ARG_ENTITIES,
            InputArgument::REQUIRED,
            SeedEntitiesService::ARG_ENTITY_DESC
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->seedEntitiesService->execute($input, $output);
    }
} 
