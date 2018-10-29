<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Service\Project;

use Eurotext\RestApiClient\Api\ProjectV1Api;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Service\Project\CreateProjectEntitiesService;
use Eurotext\TranslationManager\Service\Project\CreateProjectService;
use Eurotext\TranslationManager\Service\Project\TransitionProjectService;
use Eurotext\TranslationManager\Service\SendProjectService;
use Eurotext\TranslationManager\State\ProjectStateMachine;
use Eurotext\TranslationManager\Test\Builder\ConfigurationMockBuilder;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;

class SendProjectIntegrationTest extends IntegrationTestAbstract
{
    /** @var SendProjectService */
    private $sut;

    /** @var CreateProjectEntitiesService|\PHPUnit_Framework_MockObject_MockObject */
    private $createProjectEntities;

    protected function setUp()
    {
        parent::setUp();

        $configBuiler = new ConfigurationMockBuilder($this);
        $config       = $configBuiler->buildConfiguration();

        $projectApi = new ProjectV1Api($config);

        $createProjectEntities = $this->objectManager->create(
            CreateProjectEntitiesService::class, ['projectApi' => $projectApi]
        );
        $this->createProjectEntities = $this->getMockBuilder(CreateProjectEntitiesService::class)
             ->setMethods(['execute'])
             ->disableOriginalConstructor()
             ->getMock();

        $this->sut = new SendProjectService(
            $this->objectManager->get(ProjectRepositoryInterface::class),
            $this->objectManager->create(CreateProjectService::class, ['projectApi' => $projectApi]),
            $this->createProjectEntities,
            $this->objectManager->create(TransitionProjectService::class, ['projectApi' => $projectApi]),
            $this->objectManager->get(ProjectStateMachine::class)
        );
    }

    /**
     * @magentoDataFixture loadFixtures
     */
    public function testItShouldSendAProjectToEurotext()
    {
        $this->createProjectEntities->expects($this->once())->method('execute')->willReturn([]);

        /** @var Project $project */
        $project = $this->objectManager->create(Project::class);
        $project->isObjectNew(true);
        $project->setName('project-' . time());
        $project->setStatus(ProjectInterface::STATUS_TRANSFER);
        $project->save();

        $result = $this->sut->execute($project);

        $this->assertTrue($result);

        $this->assertNotEmpty($project->getExtId());

        $this->assertGreaterThan(0, $project->getExtId());
        $this->assertEquals(ProjectInterface::STATUS_EXPORTED, $project->getStatus());
    }

    public static function loadFixtures()
    {
        include __DIR__ . '/../_fixtures/provide_products.php';
        include __DIR__ . '/../_fixtures/provide_project.php';
    }
}
