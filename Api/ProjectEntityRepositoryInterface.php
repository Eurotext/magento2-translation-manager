<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Api;

use Eurotext\TranslationManager\Api\Data\ProjectEntityInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface ProjectEntityRepositoryInterface
{
    public function save(ProjectEntityInterface $object): ProjectEntityInterface;

    public function getById(int $id): ProjectEntityInterface;

    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface;

    public function delete(ProjectEntityInterface $object): bool;

    public function deleteById(int $id): bool;
}
