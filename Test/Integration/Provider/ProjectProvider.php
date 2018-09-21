<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Provider;

use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Magento\TestFramework\Helper\Bootstrap;

class ProjectProvider
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $objectManager;

    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct()
    {
        $this->objectManager     = Bootstrap::getObjectManager();
        $this->projectRepository = $this->objectManager->get(ProjectRepository::class);
    }

    /**
     * @param string $name
     * @param string $status
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectInterface|Project
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createProject(string $name, string $status = Project::STATUS_TRANSFER)
    {
        /** @var Project $project */
        $project = $this->objectManager->get(Project::class);
        $project->setName($name);
        $project->setStatus($status);

        return $this->projectRepository->save($project);
    }

}
