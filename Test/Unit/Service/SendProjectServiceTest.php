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
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntitySenderInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Mapper\ProjectPostMapper;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Sender\EntitySenderPool;
use Eurotext\TranslationManager\Service\SendProjectService;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class SendProjectServiceTest extends UnitTestAbstract
{
    /** @var SendProjectService */
    private $sut;

    /** @var ProjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $projectRepository;

    /** @var ProjectV1Api|\PHPUnit_Framework_MockObject_MockObject */
    private $projectApi;

    /** @var EntitySenderPool */
    private $entitySenderPool;

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

        $this->entitySenderPool = new EntitySenderPool([$this->entitySender]);

        $this->projectPostMapper = new ProjectPostMapper();

        $this->sut = $this->objectManager->getObject(
            SendProjectService::class,
            [
                'projectRepository' => $this->projectRepository,
                'projectPostMapper' => $this->projectPostMapper,
                'projectApi'        => $this->projectApi,
                'entitySenderPool'  => $this->entitySenderPool,
            ]
        );
    }

    /**
     * @throws \Eurotext\RestApiClient\Exception\ProjectApiException
     */
    public function testItShouldSendProjectPostRequestWithEntitySenders()
    {
        $projectId    = 1;
        $projectExtId = 100;

        /** @var ProjectInterface $project */
        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->once())->method('save')->with($project);

        $response = new ProjectPostResponse();
        $response->setId($projectExtId);

        $this->projectApi->expects($this->once())->method('post')->willReturn($response);

        $this->entitySender->expects($this->once())->method('send')->with($project);

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->assertArrayHasKey('project', $result);
        $this->assertEquals(1, $result['project']);

        $senderClass = \get_class($this->entitySender);
        $this->assertArrayHasKey($senderClass, $result);
        $this->assertEquals(1, $result[$senderClass]);
    }

    /**
     * @throws \Eurotext\RestApiClient\Exception\ProjectApiException
     */
    public function testItShouldStopOnErrorDuringProjectPost()
    {
        $projectId = 1;

        /** @var ProjectInterface $project */
        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->never())->method('save')->with($project);

        $this->projectApi->expects($this->once())->method('post')->willThrowException(new \Exception);

        $this->entitySender->expects($this->never())->method('send')->with($project);

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey('project', $result);
        $this->assertNotEquals(1, $result['project']);
    }

    /**
     * @throws \Eurotext\RestApiClient\Exception\ProjectApiException
     */
    public function testItShouldSendProjectPostRequestAndCatchEntitySenderException()
    {
        $projectId    = 1;
        $projectExtId = 100;

        /** @var ProjectInterface $project */
        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->once())->method('save')->with($project);

        $response = new ProjectPostResponse();
        $response->setId($projectExtId);

        $this->projectApi->expects($this->once())->method('post')->willReturn($response);

        $this->entitySender->expects($this->once())
                           ->method('send')
                           ->with($project)
                           ->willThrowException(new \Exception());

        $result = $this->sut->executeById($projectId);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->assertArrayHasKey('project', $result);
        $this->assertEquals(1, $result['project']);

        $senderClass = \get_class($this->entitySender);
        $this->assertArrayHasKey($senderClass, $result);
        $this->assertNotEquals(1, $result[$senderClass]);
    }

}