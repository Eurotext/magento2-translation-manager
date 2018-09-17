<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntityReceiverInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Receiver\EntityReceiverPool;
use Eurotext\TranslationManager\Sender\EntitySenderPool;
use Eurotext\TranslationManager\Service\Project\FetchProjectEntitiesService;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class FetchProjectEntitesUnitTest extends UnitTestAbstract
{
    const ENTITY_RECEIVER_KEY = 'entityReceiverKey';

    /** @var FetchProjectEntitiesService */
    private $sut;

    /** @var EntitySenderPool */
    private $entitySenderPool;

    /** @var EntityReceiverInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entityReceiver;

    /** @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $storeManager;

    /** @var StoreInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $store;

    protected function setUp()
    {
        parent::setUp();

        $this->entityReceiver =
            $this->getMockBuilder(EntityReceiverInterface::class)
                 ->setMethods(['receive'])
                 ->getMockForAbstractClass();

        $this->entitySenderPool = new EntityReceiverPool([self::ENTITY_RECEIVER_KEY => $this->entityReceiver]);

        $this->store = $this->getMockBuilder(StoreInterface::class)->getMock();

        $this->storeManager = $this->getMockBuilder(StoreManagerInterface::class)->getMock();
        $this->storeManager->expects($this->once())->method('getStore')->willReturn($this->store);

        $this->sut = $this->objectManager->getObject(
            FetchProjectEntitiesService::class,
            [
                'entityReceiverPool' => $this->entitySenderPool,
                'storeManager'       => $this->storeManager,
            ]
        );
    }

    public function testItShouldReceiveItemGetRequestWithEntityReceivers()
    {
        /** @var ProjectInterface $project */
        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->entityReceiver->expects($this->once())->method('receive')->with($project);

        $result = $this->sut->execute($project);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey(self::ENTITY_RECEIVER_KEY, $result);
        $this->assertEquals(1, $result[self::ENTITY_RECEIVER_KEY]);
    }

    public function testItShouldReceiveItemGetRequestAndCatchEntityReceiverException()
    {
        $exceptionMessage = 'There was was an error during Receiver exceution';

        /** @var ProjectInterface $project */
        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->entityReceiver->expects($this->once())
                             ->method('receive')
                             ->with($project)
                             ->willThrowException(new \Exception($exceptionMessage));

        $result = $this->sut->execute($project);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey(self::ENTITY_RECEIVER_KEY, $result);
        $this->assertEquals($exceptionMessage, $result[self::ENTITY_RECEIVER_KEY]);
    }

}