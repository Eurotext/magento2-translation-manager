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

}