<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Api\Data;

interface ProjectEntityInterface
{
    const STATUS_NEW      = 'new';
    const STATUS_EXPORTED = 'exported';
    const STATUS_IMPORTED = 'imported';
    const STATUS_ERROR    = 'error';

    public function getId();

    public function getProjectId(): int;

    public function getEntityId();

    public function getExtId(): int;

    public function getStatus(): string;

    public function getCreatedAt(): string;

    public function getUpdatedAt(): string;

    public function getLastError(): string;

    public function setProjectId(int $projectId);

    public function setEntityId($entityId);

    public function setExtId(int $extId);

    public function setStatus(string $status);

    public function setCreatedAt(string $createdAt);

    public function setUpdatedAt(string $updatedAt);

    public function setLastError(string $lastError);
}
