<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Console\Command\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewProjectService
{
    const ARG_NAME          = 'name';
    const ARG_STORE_ID_SRC  = 'store_id_src';
    const ARG_STORE_ID_DEST = 'store_id_dest';

    /**
     * @var \Eurotext\TranslationManager\Model\ProjectFactory
     */
    private $projectFactory;

    /**
     * @var \Eurotext\TranslationManager\Api\ProjectRepositoryInterface
     */
    private $projectRepository;

    public function __construct(
        ProjectFactory $projectFactory,
        ProjectRepositoryInterface $projectRepository
    ) {
        $this->projectFactory    = $projectFactory;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectInterface
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name        = (string)$input->getArgument(self::ARG_NAME);
        $storeIdSrc  = (int)$input->getArgument(self::ARG_STORE_ID_SRC);
        $storeIdDest = (int)$input->getArgument(self::ARG_STORE_ID_DEST);

        /** @var Project $project */
        $project = $this->projectFactory->create();
        $project->setName($name);
        $project->setStoreviewSrc($storeIdSrc);
        $project->setStoreviewDst($storeIdDest);
        $project->setStatus(ProjectInterface::STATUS_NEW);

        $project = $this->projectRepository->save($project);

        $id = $project->getId();

        $output->writeln(sprintf('project "%s" created, id: %d', $name, $id));

        return $project;
    }
}
