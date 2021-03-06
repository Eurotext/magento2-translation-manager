<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Console\Service;

use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Entity\EntitySeederPool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedEntitiesService
{
    const ARG_PROJECT_ID      = 'project-id';
    const ARG_ENTITIES        = 'entities';
    const ARG_PROJECT_ID_DESC = 'the project id';
    const ARG_ENTITY_DESC     = 'Comma seperated list of entity-types, type overview with command etm:entity:types';

    /**
     * @var EntitySeederPool
     */
    private $entitySeederPool;

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        EntitySeederPool $entitySeederPool
    ) {
        $this->entitySeederPool  = $entitySeederPool;
        $this->projectRepository = $projectRepository;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = (int)$input->getArgument(self::ARG_PROJECT_ID);
        $entities  = $input->getArgument(self::ARG_ENTITIES);

        $entityCodes = explode(',', $entities);

        $project = $this->projectRepository->getById($projectId);

        foreach ($entityCodes as $entityCode) {
            try {
                $entitySeeder = $this->entitySeederPool->getByCode($entityCode);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>%s: seeder not found</error>', $entityCode));
                continue;
            }

            $result = $entitySeeder->seed($project);

            if ($result === true) {
                $output->writeln(sprintf('<info>%s: seeding successful</info>', $entityCode));
            } else {
                $output->writeln(sprintf('<error>%s: seeding not successful</error>', $entityCode));
            }
        }

    }
}