<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Model;

use Eurotext\TranslationManager\Api\Data\ProjectProductInterface;
use Eurotext\TranslationManager\Model\ResourceModel\ProjectProductCollection;
use Eurotext\TranslationManager\Model\ResourceModel\ProjectProductResource;
use Eurotext\TranslationManager\Setup\EntitySchema\ProjectProductSchema;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class ProjectProduct
    extends AbstractModel
    implements ProjectProductInterface, IdentityInterface
{
    const CACHE_TAG = 'eurotext_project';

    protected function _construct()
    {
        $this->_init(ProjectProductResource::class);
        $this->_setResourceModel(ProjectProductResource::class, ProjectProductCollection::class);
    }

    public function getProjectId(): int
    {
        return $this->getData(ProjectProductSchema::PROJECT_ID);
    }

    public function setProjectId(int $projectId)
    {
        $this->setData(ProjectProductSchema::PROJECT_ID, $projectId);
    }

    public function getProductId(): int
    {
        return $this->getData(ProjectProductSchema::PRODUCT_ID);
    }

    public function setProductId(int $productId)
    {
        $this->setData(ProjectProductSchema::PRODUCT_ID, $productId);
    }

    public function getExtId(): int
    {
        return $this->getData(ProjectProductSchema::EXT_ID);
    }

    public function setExtId(int $extId)
    {
        $this->setData(ProjectProductSchema::EXT_ID, $extId);
    }

    public function getCreatedAt(): string
    {
        return $this->getData(ProjectProductSchema::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt)
    {
        $this->setData(ProjectProductSchema::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(ProjectProductSchema::UPDATED_AT);
    }

    public function setUpdatedAt(string $updatedAt)
    {
        $this->setData(ProjectProductSchema::UPDATED_AT, $updatedAt);
    }

    public function getLastError(): string
    {
        return $this->getData(ProjectProductSchema::LAST_ERROR);
    }

    public function setLastError(string $lastError)
    {
        $this->setData(ProjectProductSchema::LAST_ERROR, $lastError);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}
