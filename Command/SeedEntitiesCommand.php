<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Command;

use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Seeder\EntitySeederPool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedEntitiesCommand extends Command
{
    const ARG_PROJECT_ID = 'project-id';
    const ARG_ENTITIES   = 'entities';

    /**
     * @var EntitySeederPool
     */
    private $projectSeederPool;

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        EntitySeederPool $projectSeederPool
    ) {
        parent::__construct();
        $this->projectSeederPool = $projectSeederPool;
        $this->projectRepository = $projectRepository;
    }

    protected function configure()
    {
        $this->setName('etm:entity:seed');
        $this->setDescription('Seed entites for a given project');

        $this->addArgument(self::ARG_PROJECT_ID, InputArgument::REQUIRED);
        $this->addArgument(
            self::ARG_ENTITIES, InputArgument::REQUIRED,
            'Comma seperated list of entity-types, type overview with command etm:entity:types'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = (int) $input->getArgument(self::ARG_PROJECT_ID);
        $entities  = explode(',', $input->getArgument(self::ARG_ENTITIES));

        $project = $this->projectRepository->getById($projectId);

        foreach ($entities as $entityCode) {
            try {
                $projectSeeder = $this->projectSeederPool->getByCode($entityCode);
            } catch (\Exception $e) {
                $output->writeln(sprintf('%s: seeder not found', $entityCode));
                continue;
            }

            $result = $projectSeeder->seed($project);

            if ($result === true) {
                $output->writeln(sprintf('%s: seeding successful', $entityCode));
            } else {
                $output->writeln(sprintf('%s: seeding not successful', $entityCode));
            }
        }

    }
} 
