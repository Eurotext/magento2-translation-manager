<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntityDataSaverInterface;
use Eurotext\TranslationManager\Api\ProjectRepositoryInterface;
use Eurotext\TranslationManager\Entity\EntityDataSaverPool;
use Eurotext\TranslationManager\Exception\InvalidRequestException;
use Eurotext\TranslationManager\Exception\PersistanceException;
use Eurotext\TranslationManager\Model\ProjectFactory;
use Eurotext\TranslationManager\Repository\ProjectRepository;
use Eurotext\TranslationManager\Service\SaveProjectService;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;

class SaveProjectServiceTest extends UnitTestAbstract
{
    /** @var SaveProjectService */
    private $sut;

    /** @var ProjectRepository|MockObject */
    private $projectRepository;

    /** @var ProjectFactory|MockObject */
    private $projectFactory;

    /** @var EntityDataSaverPool|MockObject */
    private $entityDataSaverPool;

    /** @var DataObjectHelper|MockObject */
    private $dataObjectHelper;

    protected function setUp()
    {
        parent::setUp();

        $this->projectRepository   = $this->createMock(ProjectRepositoryInterface::class);
        $this->projectFactory      = $this->createMock(ProjectFactory::class);
        $this->entityDataSaverPool = $this->createMock(EntityDataSaverPool::class);
        $this->dataObjectHelper    = $this->createMock(DataObjectHelper::class);

        $this->sut = $this->objectManager->getObject(
            SaveProjectService::class,
            [
                'projectRepository'   => $this->projectRepository,
                'projectFactory'      => $this->projectFactory,
                'entityDataSaverPool' => $this->entityDataSaverPool,
                'dataObjectHelper'    => $this->dataObjectHelper,
            ]
        );
    }

    /**
     * @throws InvalidRequestException
     * @throws PersistanceException
     */
    public function testItShouldSaveProjectAndEntities()
    {
        $projectId = 1;

        $requestParams = [
            'project' => [
                'id' => $projectId,
            ],
        ];

        $request = $this->createMock(HttpRequest::class);
        $request->method('getParams')->willReturn($requestParams);
        $request->method('isPost')->willReturn(true);
        /** @var $request HttpRequest */

        /** @var ProjectInterface|MockObject $project */
        $project = $this->createMock(ProjectInterface::class);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->once())->method('save')->with($project);

        $this->projectFactory->expects($this->never())->method('create');

        $this->dataObjectHelper->expects($this->once())->method('populateWithArray')
                               ->with($project, $requestParams['project']);

        $entitySaver = $this->createMock(EntityDataSaverInterface::class);
        $entitySaver->expects($this->once())->method('save')->with($project, $requestParams)->willReturn(true);

        $this->entityDataSaverPool->expects($this->once())->method('getItems')->willReturn([$entitySaver]);

        $result = $this->sut->saveByRequest($request);

        $this->assertInstanceOf(ProjectInterface::class, $result);
        $this->assertEquals($project, $result);
    }

    /**
     * @throws InvalidRequestException
     * @throws PersistanceException
     */
    public function testItShouldSaveProjectWhenThereAreNoEntityDataSaver()
    {
        $projectId = 1;

        $requestParams = [
            'project' => [
                'id' => $projectId,
            ],
        ];

        $request = $this->createMock(HttpRequest::class);
        $request->method('getParams')->willReturn($requestParams);
        $request->method('isPost')->willReturn(true);
        /** @var $request HttpRequest */

        /** @var ProjectInterface|MockObject $project */
        $project = $this->createMock(ProjectInterface::class);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->once())->method('save')->with($project);

        $this->projectFactory->expects($this->never())->method('create');

        $this->dataObjectHelper->expects($this->once())->method('populateWithArray')
                               ->with($project, $requestParams['project']);

        $this->entityDataSaverPool->expects($this->once())->method('getItems')->willReturn([]);

        $result = $this->sut->saveByRequest($request);

        $this->assertInstanceOf(ProjectInterface::class, $result);
        $this->assertEquals($project, $result);
    }

    /**
     * @throws InvalidRequestException
     * @throws PersistanceException
     */
    public function testItShouldSaveProjectAndCollectEntitySaveErrors()
    {
        $this->expectException(PersistanceException::class);

        $projectId = 1;

        $requestParams = [
            'project' => [
                'id' => $projectId,
            ],
        ];

        $request = $this->createMock(HttpRequest::class);
        $request->method('getParams')->willReturn($requestParams);
        $request->method('isPost')->willReturn(true);
        /** @var $request HttpRequest */

        /** @var ProjectInterface|MockObject $project */
        $project = $this->createMock(ProjectInterface::class);
        $project->method('getId')->willReturn($projectId);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);

        $entitySaver = $this->createMock(EntityDataSaverInterface::class);
        $entitySaver->expects($this->once())->method('save')->willReturn(false);

        $entitySaverTwo = $this->createMock(EntityDataSaverInterface::class);
        $entitySaverTwo->expects($this->once())->method('save')->willReturn(true);

        $entitySavers = ['saver_1' => $entitySaver, 'saver_2' => $entitySaverTwo];
        $this->entityDataSaverPool->expects($this->once())->method('getItems')->willReturn($entitySavers);

        $result = $this->sut->saveByRequest($request);

        $this->assertInstanceOf(ProjectInterface::class, $result);
        $this->assertEquals($project, $result);
    }

    /**
     * @throws InvalidRequestException
     * @throws PersistanceException
     */
    public function testItShouldThrowExceptionIfRequestIsInvalid()
    {
        $this->expectException(InvalidRequestException::class);

        $requestParams = [];

        $request = $this->createMock(HttpRequest::class);
        $request->method('getParams')->willReturn($requestParams);
        $request->method('isPost')->willReturn(true);
        /** @var $request HttpRequest */

        $this->projectRepository->expects($this->never())->method('save');

        $this->projectFactory->expects($this->never())->method('create');

        $this->entityDataSaverPool->expects($this->never())->method('getItems');

        $this->sut->saveByRequest($request);
    }

    /**
     * @throws InvalidRequestException
     * @throws PersistanceException
     */
    public function testItShouldCreateNewObjectUsingFactory()
    {
        $projectId = 0;

        $requestParams = [
            'project' => [
                'id' => $projectId,
            ],
        ];

        $request = $this->createMock(HttpRequest::class);
        $request->method('getParams')->willReturn($requestParams);
        $request->method('isPost')->willReturn(true);
        /** @var $request HttpRequest */

        /** @var ProjectInterface|MockObject $project */
        $project = $this->createMock(ProjectInterface::class);

        $this->projectRepository->expects($this->never())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->once())->method('save')->with($project);

        $this->projectFactory->expects($this->once())->method('create')->willReturn($project)->with($requestParams);

        $this->dataObjectHelper->expects($this->once())->method('populateWithArray')
                               ->with($project, $requestParams['project']);

        $entitySaver = $this->createMock(EntityDataSaverInterface::class);
        $entitySaver->expects($this->once())->method('save')->with($project, $requestParams)->willReturn(true);

        $entitySavers = ['saver_1' => $entitySaver];
        $this->entityDataSaverPool->expects($this->once())->method('getItems')->willReturn($entitySavers);

        $result = $this->sut->saveByRequest($request);

        $this->assertInstanceOf(ProjectInterface::class, $result);
        $this->assertEquals($project, $result);
    }

    /**
     * @throws InvalidRequestException
     * @throws PersistanceException
     */
    public function testItShouldThrowPersistanceExceptionForNoSuchEntityException()
    {
        $this->expectException(PersistanceException::class);

        $projectId     = 1;
        $requestParams = ['project' => ['id' => $projectId]];

        $request = $this->createMock(HttpRequest::class);
        $request->method('getParams')->willReturn($requestParams);
        $request->method('isPost')->willReturn(true);
        /** @var $request HttpRequest */

        $this->projectRepository->expects($this->once())->method('getById')
                                ->with($projectId)->willThrowException(new NoSuchEntityException());

        $this->sut->saveByRequest($request);
    }

    /**
     * @throws InvalidRequestException
     * @throws PersistanceException
     */
    public function testItShouldThrowPersistanceExceptionForCouldNotSaveException()
    {
        $this->expectException(PersistanceException::class);

        $projectId     = 1;
        $requestParams = ['project' => ['id' => $projectId]];

        $request = $this->createMock(HttpRequest::class);
        $request->method('getParams')->willReturn($requestParams);
        $request->method('isPost')->willReturn(true);
        /** @var $request HttpRequest */

        /** @var ProjectInterface|MockObject $project */
        $project = $this->createMock(ProjectInterface::class);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->once())->method('save')
                                ->with($project)->willThrowException(new CouldNotSaveException('asdf'));

        $this->dataObjectHelper->expects($this->once())->method('populateWithArray')
                               ->with($project, $requestParams['project']);

        $this->sut->saveByRequest($request);
    }

    /**
     * @throws InvalidRequestException
     * @throws PersistanceException
     */
    public function testItShouldThrowPersistanceExceptionForException()
    {
        $this->expectException(PersistanceException::class);

        $projectId     = 1;
        $requestParams = ['project' => ['id' => $projectId]];

        $request = $this->createMock(HttpRequest::class);
        $request->method('getParams')->willReturn($requestParams);
        $request->method('isPost')->willReturn(true);
        /** @var $request HttpRequest */

        /** @var ProjectInterface|MockObject $project */
        $project = $this->createMock(ProjectInterface::class);

        $this->projectRepository->expects($this->once())->method('getById')->with($projectId)->willReturn($project);
        $this->projectRepository->expects($this->never())->method('save');

        $this->dataObjectHelper->expects($this->once())->method('populateWithArray')
                               ->with($project, $requestParams['project'])->willThrowException(new \Exception());

        $this->sut->saveByRequest($request);
    }

}