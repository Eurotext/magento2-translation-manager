<?php

namespace Eurotext\TranslationManager\Api;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface ProjectRepositoryInterface
{
    public function save(ProjectInterface $project): ProjectInterface;

    public function getById(int $id): ProjectInterface;

    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface;

    public function delete(ProjectInterface $project): bool;

    public function deleteById(int $id): bool;
}
