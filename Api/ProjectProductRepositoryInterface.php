<?php

namespace Eurotext\TranslationManager\Api;

use Eurotext\TranslationManager\Api\Data\ProjectProductInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface ProjectProductRepositoryInterface
{
    public function save(ProjectProductInterface $object): ProjectProductInterface;

    public function getById(int $id): ProjectProductInterface;

    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface;

    public function delete(ProjectProductInterface $object): bool;

    public function deleteById(int $id): bool;
}
