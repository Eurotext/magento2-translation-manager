<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Command\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Eurotext\TranslationManager\Seeder\EntitySeederPool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProjectService
{
    public const ARG_NAME = 'name';
    public const ARG_STORE_ID_SRC = 'store_id_src';
    public const ARG_STORE_ID_DEST = 'store_id_dest';

    /**
     * @var \Eurotext\TranslationManager\Model\ProjectFactory
     */
    private $projectFactory;

    /**
     * @var \Eurotext\TranslationManager\Api\ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var \Eurotext\TranslationManager\Seeder\EntitySeederPool
     */
    private $projectSeederPool;

    public function __construct(
        ProjectFactory $projectFactory,
        ProjectRepositoryInterface $projectRepository,
        EntitySeederPool $projectSeederPool
    ) {
        $this->projectFactory = $projectFactory;
        $this->projectRepository = $projectRepository;
        $this->projectSeederPool = $projectSeederPool;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectInterface
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument(self::ARG_NAME);
        $storeIdSrc = $input->getArgument(self::ARG_STORE_ID_SRC);
        $storeIdDest = $input->getArgument(self::ARG_STORE_ID_DEST);

        /** @var Project $project */
        $project = $this->projectFactory->create();
        $project->setName($name);
        $project->setStoreviewSrc($storeIdSrc);
        $project->setStoreviewDst($storeIdDest);

        $project = $this->projectRepository->save($project);

        $id = $project->getId();

        $output->writeln(sprintf('project "%s" created, id: %d', $name, $id));

        $projectSeeders = $this->projectSeederPool->getItems();
        if (count($projectSeeders) === 0) {
            $output->writeln('no seeders found');

            return $project;
        }

        // Iterate seeders to prefill project config tables
        foreach ($projectSeeders as $projectSeeder) {
            $result = $projectSeeder->seed($project);

            $seederClass = \get_class($projectSeeder);

            if ($result === true) {
                $output->writeln(sprintf('seeding for "%s" successful', $seederClass));
            } else {
                $output->writeln(sprintf('seeding for "%s" not successful', $seederClass));
            }
        }

        return $project;
    }
}