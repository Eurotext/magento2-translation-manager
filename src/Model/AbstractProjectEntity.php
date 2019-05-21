<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Model;

use Eurotext\TranslationManager\Api\Data\ProjectEntityInterface;
use Eurotext\TranslationManager\Api\Setup\ProjectEntitySchema;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

abstract class AbstractProjectEntity
    extends AbstractModel
    implements ProjectEntityInterface, IdentityInterface
{
    abstract protected function getCacheTag(): string;

    public function getId()
    {
        return parent::getId() === null ? null : (int)parent::getId();
    }

    public function getProjectId(): int
    {
        return (int)$this->getData(ProjectEntitySchema::PROJECT_ID) ?: 0;
    }

    public function setProjectId(int $projectId)
    {
        $this->setData(ProjectEntitySchema::PROJECT_ID, $projectId);
    }

    public function getEntityId(): int
    {
        return (int)$this->getData(ProjectEntitySchema::ENTITY_ID) ?: 0;
    }

    public function setEntityId($entityId)
    {
        $this->setData(ProjectEntitySchema::ENTITY_ID, $entityId);
    }

    public function getExtId(): int
    {
        return (int)$this->getData(ProjectEntitySchema::EXT_ID) ?: 0;
    }

    public function setExtId(int $extId)
    {
        $this->setData(ProjectEntitySchema::EXT_ID, $extId);
    }

    public function getStatus(): string
    {
        return $this->getData(ProjectEntitySchema::STATUS) ?: '';
    }

    public function setStatus(string $status)
    {
        $this->setData(ProjectEntitySchema::STATUS, $status);
    }

    public function getCreatedAt(): string
    {
        return $this->getData(ProjectEntitySchema::CREATED_AT) ?: '';
    }

    public function setCreatedAt(string $createdAt)
    {
        $this->setData(ProjectEntitySchema::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(ProjectEntitySchema::UPDATED_AT) ?: '';
    }

    public function setUpdatedAt(string $updatedAt)
    {
        $this->setData(ProjectEntitySchema::UPDATED_AT, $updatedAt);
    }

    public function getLastError(): string
    {
        return $this->getData(ProjectEntitySchema::LAST_ERROR) ?: '';
    }

    public function setLastError(string $lastError)
    {
        $this->setData(ProjectEntitySchema::LAST_ERROR, $lastError);
    }

    public function getIdentities()
    {
        return [$this->getCacheTag() . '_' . $this->getId()];
    }

}
