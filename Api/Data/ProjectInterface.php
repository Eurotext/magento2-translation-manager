<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Api\Data;

interface ProjectInterface
{
    const STATUS_NEW = 'new';

    public function getId();

    public function getExtId(): int;

    public function getCode(): string;

    public function getName(): string;

    public function getStatus(): string;

    public function getStoreviewSrc(): int;

    public function getStoreviewDst(): int;

    public function getCustomerComment(): string;

    public function getCreatedAt(): string;

    public function getUpdatedAt(): string;

    public function getLastError(): string;

    public function setExtId(int $extId);

    public function setCode(string $code);

    public function setName(string $name);

    public function setStatus(string $name);

    public function setStoreviewSrc(int $storeId);

    public function setStoreviewDst(int $storeId);

    public function setCustomerComment(string $comment);

    public function setCreatedAt(string $createdAt);

    public function setUpdatedAt(string $updatedAt);

    public function setLastError(string $lastError);
}
