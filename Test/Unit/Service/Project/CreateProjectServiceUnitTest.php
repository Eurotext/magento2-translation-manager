<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service;

use Eurotext\RestApiClient\Api\ProjectV1Api;
use Eurotext\RestApiClient\Response\ProjectPostResponse;
use Eurotext\TranslationManager\Api\EntitySenderInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Mapper\ProjectPostMapper;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Service\Project\CreateProjectService;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class CreateProjectServiceUnitTest extends UnitTestAbstract
{
    /** @var CreateProjectService */
    private $sut;

    /** @var ProjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    /** @var ProjectV1Api|\PHPUnit_Framework_MockObject_MockObject */
    private $projectApi;

    /** @var ProjectPostMapper */
    private $projectPostMapper;

    /** @var EntitySenderInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entitySender;

    protected function setUp()
    {
        parent::setUp();

        $this->projectRepository =
            $this->getMockBuilder(ProjectRepositoryInterface::class)
                 ->setMethods(['getById'])
                 ->getMockForAbstractClass();

        $this->projectApi =
            $this->getMockBuilder(ProjectV1Api::class)
                 ->setMethods(['post'])
                 ->getMock();

        $this->entitySender =
            $this->getMockBuilder(EntitySenderInterface::class)
                 ->setMethods(['send'])
                 ->getMockForAbstractClass();

        $this->projectPostMapper = new ProjectPostMapper();

        $this->sut = $this->objectManager->getObject(
            CreateProjectService::class,
            [
                'projectRepository' => $this->projectRepository,
                'projectPostMapper' => $this->projectPostMapper,
                'projectApi'        => $this->projectApi,
            ]
        );
    }

    public function testItShouldSendProjectPostRequest()
    {
        $projectId    = 1;
        $projectExtId = 100;

        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['getExtId'])
                        ->getMock();

        $project->expects($this->once())->method('getExtId')->willReturn(0);
        /** @var Project $project */

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->once())->method('save')->with($project);

        $response = new ProjectPostResponse();
        $response->setId($projectExtId);

        $this->projectApi->expects($this->once())->method('post')->willReturn($response);

        $result = $this->sut->executeById($projectId);

        $this->assertTrue($result);
    }

    public function testItShouldStopOnErrorDuringProjectPost()
    {
        $projectId = 1;

        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['getExtId'])
                        ->getMock();
        $project->expects($this->once())->method('getExtId')->willReturn(0);
        /** @var Project $project */

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->never())->method('save')->with($project);

        $this->projectApi->expects($this->once())->method('post')->willThrowException(new \Exception);

        $this->entitySender->expects($this->never())->method('send')->with($project);

        $result = $this->sut->executeById($projectId);

        $this->assertFalse($result);
    }


    public function testItShouldStopWhenProjectAlreadyHasExtId()
    {
        $projectExtId = 100;

        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['getExtId'])
                        ->getMock();

        $project->expects($this->once())->method('getExtId')->willReturn($projectExtId);
        /** @var Project $project */

        $this->projectApi->expects($this->never())->method('post');

        $result = $this->sut->execute($project);

        $this->assertTrue($result);
    }
}