<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntityRetrieverInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Retriever\EntityRetrieverPool;
use Eurotext\TranslationManager\Sender\EntitySenderPool;
use Eurotext\TranslationManager\Service\Project\FetchProjectEntitiesService;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class FetchProjectEntitesUnitTest extends UnitTestAbstract
{
    const ENTITY_RECEIVER_KEY = 'entityRetrieverKey';

    /** @var FetchProjectEntitiesService */
    private $sut;

    /** @var EntitySenderPool */
    private $entitySenderPool;

    /** @var EntityRetrieverInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entityRetriever;

    /** @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $storeManager;

    /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $store;

    protected function setUp()
    {
        parent::setUp();

        $this->entityRetriever = $this->createMock(EntityRetrieverInterface::class);

        $this->entitySenderPool = new EntityRetrieverPool([self::ENTITY_RECEIVER_KEY => $this->entityRetriever]);

        $this->store = $this->createMock(StoreInterface::class);

        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->storeManager->expects($this->once())->method('getStore')->willReturn($this->store);

        $this->sut = $this->objectManager->getObject(
            FetchProjectEntitiesService::class,
            [
                'entityRetrieverPool' => $this->entitySenderPool,
                'storeManager'        => $this->storeManager,
            ]
        );
    }

    public function testItShouldRetrieveItemGetRequestWithEntityRetrievers()
    {
        /** @var ProjectInterface $project */
        $project = $this->createMock(ProjectInterface::class);
        $this->entityRetriever->expects($this->once())->method('retrieve')->with($project);

        $result = $this->sut->execute($project);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey(self::ENTITY_RECEIVER_KEY, $result);
        $this->assertEquals(1, $result[self::ENTITY_RECEIVER_KEY]);
    }

    public function testItShouldRetrieveItemGetRequestAndCatchEntityRetrieverException()
    {
        $exceptionMessage = 'There was was an error during Retriever exceution';

        /** @var ProjectInterface $project */
        $project = $this->createMock(ProjectInterface::class);
        $this->entityRetriever->expects($this->once())
                              ->method('retrieve')
                              ->with($project)
                              ->willThrowException(new \Exception($exceptionMessage));

        $result = $this->sut->execute($project);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey(self::ENTITY_RECEIVER_KEY, $result);
        $this->assertEquals($exceptionMessage, $result[self::ENTITY_RECEIVER_KEY]);
    }

}