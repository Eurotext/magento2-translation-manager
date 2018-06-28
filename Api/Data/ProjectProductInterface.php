<?php

namespace Eurotext\TranslationManager\Api\Data;

interface ProjectProductInterface
{
    public function getId();

    public function getProjectId(): int;

    public function getProductId(): int;

    public function getExtId(): int;

    public function getCreatedAt(): string;

    public function getUpdatedAt(): string;

    public function getLastError(): string;

    public function setProjectId(int $projectId);

    public function setProductId(int $productId);

    public function setExtId(int $extId);

    public function setCreatedAt(string $createdAt);

    public function setUpdatedAt(string $updatedAt);

    public function setLastError(string $lastError);
}
