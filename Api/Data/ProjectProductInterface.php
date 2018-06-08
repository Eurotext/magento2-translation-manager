<?php

namespace Eurotext\TranslationManager\Api\Data;

interface ProjectProductInterface
{
    public function getId();

    public function getProjectId(): ?int;

    public function getProductId(): ?int;

    public function getExtId(): ?int;

    public function getCreatedAt(): ?string;

    public function getUpdatedAt(): ?string;

    public function getLastError(): ?string;

    public function setProjectId(int $projectId): void;

    public function setProductId(int $productId): void;

    public function setExtId(int $extId): void;

    public function setCreatedAt(string $createdAt): void;

    public function setUpdatedAt(string $updatedAt): void;

    public function setLastError(string $lastError): void;
}