<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Service\Project;

use Eurotext\TranslationManager\ApiClient\Configuration;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Service\Project\CreateProjectService;
use Eurotext\TranslationManager\Test\Integration\IntegrationTestAbstract;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;

class CreateProjectServiceIntegrationTest extends IntegrationTestAbstract
{
    /** @var CreateProjectService */
    protected $sut;

    protected function setUp()
    {
        parent::setUp();

        /** @var ConfigResource $config */
        $config = $this->objectManager->get(ConfigResource::class);
        $config->saveConfig(Configuration::CONFIG_PATH_API_KEY, \constant('EUROTEXT_API_KEY'), 'default', 0);
        $config->saveConfig(Configuration::CONFIG_PATH_API_HOST, 'https://sandbox.api.eurotext.de', 'default', 0);
        $config->saveConfig(Configuration::CONFIG_PATH_API_DEBUG_MODE, '0', 'default', 0);

        $this->sut = $this->objectManager->get(CreateProjectService::class);
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
