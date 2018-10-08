<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service;

use Eurotext\RestApiClient\Api\ProjectV1Api;
use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Response\ProjectTransitionResponse;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Service\Project\TransitionProjectService;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Psr\Http\Message\ResponseInterface;

class TransitionProjectUnitTest extends UnitTestAbstract
{
    /** @var TransitionProjectService */
    private $sut;

    /** @var ProjectV1Api|\PHPUnit_Framework_MockObject_MockObject */
    private $projectApi;

    protected function setUp()
    {
        parent::setUp();

        $this->projectApi =
            $this->getMockBuilder(ProjectV1Api::class)
                 ->setMethods(['transition'])
                 ->getMock();

        $this->sut = $this->objectManager->getObject(
            TransitionProjectService::class,
            [
                'projectApi' => $this->projectApi,
            ]
        );
    }

    public function testItShouldSendProjectTransitionRequest()
    {
        $projectExtId = 10;

        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['getExtId', 'setLastError'])
                        ->getMock();
        $project->expects($this->once())->method('getExtId')->willReturn($projectExtId);
        $project->expects($this->never())->method('setLastError');
        /** @var Project $project */

        $httpResponse = $this->getMockBuilder(ResponseInterface::class)->getMockForAbstractClass();
        $httpResponse->expects($this->once())->method('getStatusCode')->willReturn(200);
        /** @var ResponseInterface $httpResponse */

        $response = new ProjectTransitionResponse();
        $response->setHttpResponse($httpResponse);

        $this->projectApi->expects($this->once())->method('transition')->willReturn($response);

        $result = $this->sut->execute($project, ProjectStatusEnum::NEW());

        $this->assertTrue($result);
    }

    public function testItShouldHandleHttpStatusCodesFromHTTPResponse()
    {
        $projectExtId = 10;

        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['getExtId', 'setLastError'])
                        ->getMock();
        $project->expects($this->once())->method('getExtId')->willReturn($projectExtId);
        $project->expects($this->once())->method('setLastError');
        /** @var Project $project */

        $httpResponse = $this->getMockBuilder(ResponseInterface::class)->getMockForAbstractClass();
        $httpResponse->expects($this->once())->method('getStatusCode')->willReturn(404);
        /** @var ResponseInterface $httpResponse */

        $response = new ProjectTransitionResponse();
        $response->setHttpResponse($httpResponse);

        $this->projectApi->expects($this->once())->method('transition')->willReturn($response);

        $result = $this->sut->execute($project, ProjectStatusEnum::NEW());

        $this->assertFalse($result);
    }

    public function testItShouldHandleAGuzzleException()
    {
        $projectExtId = 10;

        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['getExtId', 'setLastError'])
                        ->getMock();
        $project->expects($this->once())->method('getExtId')->willReturn($projectExtId);
        $project->expects($this->once())->method('setLastError');
        /** @var Project $project */

        $this->projectApi->expects($this->once())->method('transition')
                         ->willThrowException(new \GuzzleHttp\Exception\TransferException());

        $result = $this->sut->execute($project, ProjectStatusEnum::NEW());

        $this->assertFalse($result);
    }
}