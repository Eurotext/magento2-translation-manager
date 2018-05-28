<?php

namespace Eurotext\TranslationManager\Api\Data;

interface ProjectInterface
{
    public function getExtId(): ?int;

    public function getCode(): ?string;

    public function getName(): ?string;

    public function getStoreviewSrc(): ?int;

    public function getStoreviewDst(): ?int;

    public function getCustomerComment(): ?string;

    public function getCreatedAt(): ?string;

    public function getUpdatedAt(): ?string;

    public function getLastError(): ?string;

    public function setExtId(int $extId): void;

    public function setCode(string $code): void;

    public function setName(string $name): void;

    public function setStoreviewSrc(int $storeId): void;

    public function setStoreviewDst(int $storeId): void;

    public function setCustomerComment(string $comment): void;

    public function setCreatedAt(string $createdAt): void;

    public function setUpdatedAt(string $updatedAt): void;

    public function setLastError(string $lastError): void;
}