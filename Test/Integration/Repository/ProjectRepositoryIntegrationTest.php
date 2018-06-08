<?php
/**
 * @copyright see LICENSE.txt
 *
 * @see LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Repository;

use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;
use Magento\Framework\Exception\NoSuchEntityException;

class ProjectRepositoryIntegrationTest extends IntegrationTestAbstract
{
    /** @var ProjectRepository */
    protected $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = $this->objectManager->get(ProjectRepository::class);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testItShouldCreateAProjectAndGetItById()
    {
        $name = 'integration-test-project-1';
        $project = $this->createProject($name);

        $id = $project->getId();
        $code = $project->getCode();

        $this->assertTrue($id > 0);
        $this->assertNotSame('', $code);

        $projectRead = $this->sut->getById($id);

        $this->assertSame($id, $projectRead->getId());
        $this->assertSame($code, $projectRead->getCode());
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testItShouldDeleteProjects()
    {
        $name = 'integration-test-project-for-deletion';
        $project = $this->createProject($name);

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
        $names = ['integration-test-project-1'];

        $projects = [];
        foreach ($names as $name) {
            $project = $this->createProject($name);

            $projects[$name] = $project;
        }

        /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->objectManager->get(\Magento\Framework\Api\SearchCriteria::class);

        $searchResults = $this->sut->getList($searchCriteria);

        $items = $searchResults->getItems();
        $this->assertTrue(count($items) >= count($projects));
    }

    /**
     * @param $name
     *
     * @return \Eurotext\TranslationManager\Api\Data\ProjectInterface|\Eurotext\TranslationManager\Model\Project
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function createProject($name)
    {
        /** @var Project $project */
        $project = $this->objectManager->get(Project::class);
        $project->setName($name);

        $project = $this->sut->save($project);

        return $project;
    }
}