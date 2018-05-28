<?php

namespace Eurotext\TranslationManager\Api;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ProjectRepositoryInterface
{
    public function save(ProjectInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(ProjectInterface $page);

    public function deleteById($id);
}
