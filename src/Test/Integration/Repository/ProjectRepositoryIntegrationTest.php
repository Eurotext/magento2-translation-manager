<?php
declare(strict_types=1);
/**
 * @copyright see LICENSE.txt
 *
 * @see LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Repository;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;
use Eurotext\TranslationManager\Test\Integration\Provider\ProjectProvider;
use Magento\Framework\Exception\NoSuchEntityException;

class ProjectRepositoryIntegrationTest extends IntegrationTestAbstract
{
    /** @var ProjectRepository */
    private $sut;

    /** @var ProjectProvider */
    private $projectProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = $this->objectManager->get(ProjectRepository::class);

        $this->projectProvider = $this->objectManager->get(ProjectProvider::class);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testItShouldCreateAProjectAndGetItById()
    {
        $name    = 'integration-test-project-1';
        $project = $this->projectProvider->createProject($name);

        $id   = $project->getId();
        $code = $project->getCode();

        $this->assertTrue($id > 0);
        $this->assertNotSame('', $code);

        $projectRead = $this->sut->getById($id);

        $this->assertEquals($id, $projectRead->getId());
        $this->assertEquals($code, $projectRead->getCode());
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testItShouldDeleteProjects()
    {
        $name    = 'integration-test-project-for-deletion';
        $project = $this->projectProvider->createProject($name);

        $id = $project->getId();

        $result = $this->sut->deleteById($id);

        $this->assertTrue($result);

        try {
            $projectRead = $this->sut->getById($id);
        } catch (NoSuchEntityException $e) {
            $projectRead = null;
        }

        $this->assertNull($projectRead);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testItShouldReturnAListOfProjects()
    {
        $projectName = 'integration-test-project-1';

        $names = [$projectName];

        $projects = [];
        foreach ($names as $name) {
            $project = $this->projectProvider->createProject($name);

            $projects[$name] = $project;
        }

        /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->objectManager->get(\Magento\Framework\Api\SearchCriteria::class);

        $searchResults = $this->sut->getList($searchCriteria);

        $items = $searchResults->getItems();

        $result = false;
        foreach ($items as $item) {
            /** @var $item ProjectInterface */
            $result = $item->getName() === $projectName;
            if ($result === true) {
                break;
            }
        }

        $this->assertTrue($result, 'Project was not returned by the repository');
    }
}
