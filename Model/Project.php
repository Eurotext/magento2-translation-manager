<?php

namespace Eurotext\TranslationManager\Model;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Model\ResourceModel\ProjectCollection;
use Eurotext\TranslationManager\Model\ResourceModel\ProjectResource;
use Eurotext\TranslationManager\Setup\ProjectSchema;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Project
    extends AbstractModel
    implements ProjectInterface, IdentityInterface
{
    public const CACHE_TAG = 'eurotext_project';

    protected function _construct()
    {
        $this->_init(ProjectResource::class);
        $this->_setResourceModel(ProjectResource::class, ProjectCollection::class);
    }

    public function getExtId(): ?int
    {
        return $this->getData(ProjectSchema::EXT_ID);
    }

    public function getCode(): ?string
    {
        return $this->getData(ProjectSchema::CODE);
    }

    public function getName(): ?string
    {
        return $this->getData(ProjectSchema::NAME);
    }

    public function getStoreviewSrc(): ?int
    {
        return $this->getData(ProjectSchema::STOREVIEW_SRC);
    }

    public function getStoreviewDst(): ?int
    {
        return $this->getData(ProjectSchema::STOREVIEW_DST);
    }

    public function getCustomerComment(): ?string
    {
        return $this->getData(ProjectSchema::CUSTOMER_COMMENT);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(ProjectSchema::CREATED_AT);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(ProjectSchema::UPDATED_AT);
    }

    public function getLastError(): ?string
    {
        return $this->getData(ProjectSchema::LAST_ERROR);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
