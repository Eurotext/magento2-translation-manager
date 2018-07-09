<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Service\Project;

use Eurotext\RestApiClient\Api\ProjectV1Api;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\ApiClient\Configuration;
use Eurotext\TranslationManager\Mapper\ProjectPostMapper;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Service\Project\CreateProjectService;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;
use Psr\Log\LoggerInterface;

class CreateProjectServiceIntegrationTest extends IntegrationTestAbstract
{
    /** @var CreateProjectService */
    protected $sut;

    protected function setUp()
    {
        parent::setUp();

        $config = $this->getMockBuilder(Configuration::class)
                       ->disableOriginalConstructor()
                       ->setMethods(['getApiKey','getHost', 'getDebug'])
                       ->getMock();
        $config->expects($this->once())->method('getApiKey')->willReturn(\constant('EUROTEXT_API_KEY'));
        $config->expects($this->once())->method('getHost')->willReturn('https://sandbox.api.eurotext.de');
        $config->expects($this->once())->method('getDebug')->willReturn(false);

        $projectApi = new ProjectV1Api($config);

        $this->sut = new CreateProjectService(
            $this->objectManager->get(ProjectRepositoryInterface::class),
            $this->objectManager->get(ProjectPostMapper::class),
            $projectApi,
            $this->objectManager->get(LoggerInterface::class)
        );
    }

    /**
     * @magentoDataFixture loadFixtures
     *
     * @test
     */
    public function itShouldSendAProjectToEurotext()
    {
        $project = $this->objectManager->create(Project::class);
        $project->isObjectNew(true);
        $project->setName('project-' . time());
        $project->save();

        $result = $this->sut->execute($project);

        $this->assertTrue($result);

        $this->assertNotEmpty($project->getExtId());
    }

    public static function loadFixtures()
    {
        include __DIR__ . '/../../_fixtures/provide_products.php';
        include __DIR__ . '/../../_fixtures/provide_project.php';
    }
}
