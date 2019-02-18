<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Console\Service;

use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Entity\EntitySeederPool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddEntityService
{
    const ARG_PROJECT_ID  = 'project-id';
    const ARG_ENTITY_TYPE = 'entity-type';
    const ARG_ENTITY_ID   = 'entity-id';

    const ARG_PROJECT_ID_DESC  = 'the project id';
    const ARG_ENTITY_TYPE_DESC = 'the entity-type you want to import, type overview with command etm:entity:types';
    const ARG_ENTITY_ID_DESC   = 'Comma seperated list of entities to add. Depending on the entity-type the data provided may vary.';

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var EntitySeederPool
     */
    private $entitySeederPool;

    public function __construct(ProjectRepositoryInterface $projectRepository, EntitySeederPool $entitySeederPool)
    {
        $this->projectRepository = $projectRepository;
        $this->entitySeederPool  = $entitySeederPool;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId  = (int)$input->getArgument(self::ARG_PROJECT_ID);
        $entityCode = (string)$input->getArgument(self::ARG_ENTITY_TYPE);
        $entityId   = (string)$input->getArgument(self::ARG_ENTITY_ID);

        $entities = explode(',', $entityId);

        $project = $this->projectRepository->getById($projectId);

        try {
            $entitySeeder = $this->entitySeederPool->getByCode($entityCode);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s: seeder not found</error>', $entityCode));

            return;
        }

        $result = $entitySeeder->seed($project, $entities);

        if ($result === true) {
            $output->writeln(sprintf('<info>%s: seeding successful</info>', $entityCode));
        } else {
            $output->writeln(sprintf('<error>%s: seeding not successful</error>', $entityCode));
        }

    }
} 
