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
use Eurotext\TranslationManager\Entity\EntitySenderPool;
use Eurotext\TranslationManager\Service\Project\CreateProjectEntitiesService;
use Eurotext\TranslationManager\Test\Unit\UnitTestAbstract;

class CreateProjectEntitesUnitTest extends UnitTestAbstract
{
    const ENTITY_SENDER_KEY = 'entitySenderKey';

    /** @var CreateProjectEntitiesService */
    private $sut;

    /** @var EntitySenderPool */
    private $entitySenderPool;

    /** @var EntitySenderInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entitySender;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entitySender = $this->createMock(EntitySenderInterface::class);

        $this->entitySenderPool = new EntitySenderPool([self::ENTITY_SENDER_KEY => $this->entitySender]);

        $this->sut = $this->objectManager->getObject(
            CreateProjectEntitiesService::class,
            [
                'entitySenderPool' => $this->entitySenderPool,
            ]
        );
    }

    /**
     * @throws \Eurotext\RestApiClient\Exception\ApiClientException
     */
    public function testItShouldSendProjectPostRequestWithEntitySenders()
    {
        /** @var ProjectInterface $project */
        $project = $this->createMock(ProjectInterface::class);

        $this->entitySender->expects($this->once())->method('send')->with($project);

        $result = $this->sut->execute($project);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey(self::ENTITY_SENDER_KEY, $result);
        $this->assertEquals(1, $result[self::ENTITY_SENDER_KEY]);
    }

    /**
     * @throws \Eurotext\RestApiClient\Exception\ApiClientException
     */
    public function testItShouldSendProjectPostRequestAndCatchEntitySenderException()
    {
        $exceptionMessage = 'There was was an error during Sender exceution';

        /** @var ProjectInterface $project */
        $project = $this->createMock(ProjectInterface::class);

        $this->entitySender->expects($this->once())
                           ->method('send')
                           ->with($project)
                           ->willThrowException(new \Exception($exceptionMessage));

        $result = $this->sut->execute($project);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey(self::ENTITY_SENDER_KEY, $result);
        $this->assertEquals($exceptionMessage, $result[self::ENTITY_SENDER_KEY]);
    }

}
