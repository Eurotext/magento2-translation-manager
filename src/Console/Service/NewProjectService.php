<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Console\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewProjectService
{
    const ARG_NAME       = 'name';
    const ARG_STORE_SRC  = 'store_src';
    const ARG_STORE_DEST = 'store_dest';

    /**
     * @var \Eurotext\TranslationManager\Model\ProjectFactory
     */
    private $projectFactory;

    /**
     * @var \Eurotext\TranslationManager\Api\ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    public function __construct(
        ProjectFactory $projectFactory,
        ProjectRepositoryInterface $projectRepository,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->projectFactory    = $projectFactory;
        $this->projectRepository = $projectRepository;
        $this->storeRepository   = $storeRepository;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name      = (string)$input->getArgument(self::ARG_NAME);
        $storeSrc  = (string)$input->getArgument(self::ARG_STORE_SRC);
        $storeDest = (string)$input->getArgument(self::ARG_STORE_DEST);

        $storeIdSrc  = $this->getStoreIdByStoreCode($storeSrc);
        $storeIdDest = $this->getStoreIdByStoreCode($storeDest);

        /** @var Project $project */
        $project = $this->projectFactory->create();
        $project->setName($name);
        $project->setStoreviewSrc($storeIdSrc);
        $project->setStoreviewDst($storeIdDest);
        $project->setStatus(ProjectInterface::STATUS_NEW);

        $project = $this->projectRepository->save($project);

        $id = $project->getId();

        $output->writeln(sprintf('<info>project "%s" created, id: %d</info>', $name, $id));

        return $project;
    }

    /**
     * @param string $storeCode
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStoreIdByStoreCode(string $storeCode): int
    {
        $store = $this->storeRepository->get($storeCode);

        return (int)$store->getId();
    }
}
