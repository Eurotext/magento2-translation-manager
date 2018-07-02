<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Unit\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Api\EntitySenderInterface;
use Eurotext\TranslationManager\Model\Project;
use Eurotext\TranslationManager\Sender\EntitySenderPool;
use Eurotext\TranslationManager\Service\Project\CreateProjectEntitiesService;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class CreateProjectEntitesUnitTest extends UnitTestAbstract
{
    /** @var CreateProjectEntitiesService */
    private $sut;

    /** @var EntitySenderPool */
    private $entitySenderPool;

    /** @var EntitySenderInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entitySender;

    protected function setUp()
    {
        parent::setUp();

        $this->entitySender =
            $this->getMockBuilder(EntitySenderInterface::class)
                 ->setMethods(['send'])
                 ->getMockForAbstractClass();

        $this->entitySenderPool = new EntitySenderPool([$this->entitySender]);

        $this->sut = $this->objectManager->getObject(
            CreateProjectEntitiesService::class,
            [
                'entitySenderPool' => $this->entitySenderPool,
            ]
        );
    }

    /**
     * @throws \Eurotext\RestApiClient\Exception\ProjectApiException
     */
    public function testItShouldSendProjectPostRequestWithEntitySenders()
    {
        /** @var ProjectInterface $project */
        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->entitySender->expects($this->once())->method('send')->with($project);

        $result = $this->sut->execute($project);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $senderClass = \get_class($this->entitySender);
        $this->assertArrayHasKey($senderClass, $result);
        $this->assertEquals(1, $result[$senderClass]);
    }

    /**
     * @throws \Eurotext\RestApiClient\Exception\ProjectApiException
     */
    public function testItShouldSendProjectPostRequestAndCatchEntitySenderException()
    {
        /** @var ProjectInterface $project */
        $project = $this->getMockBuilder(Project::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->entitySender->expects($this->once())
                           ->method('send')
                           ->with($project)
                           ->willThrowException(new \Exception());

        $result = $this->sut->execute($project);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $senderClass = \get_class($this->entitySender);
        $this->assertArrayHasKey($senderClass, $result);
        $this->assertNotEquals(1, $result[$senderClass]);
    }

}