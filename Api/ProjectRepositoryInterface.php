<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Api;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface ProjectRepositoryInterface
{
    /**
     * @param ProjectInterface $project
     *
     * @return ProjectInterface
     * @throws CouldNotSaveException
     */
    public function save(ProjectInterface $project): ProjectInterface;

    /**
     * @param int $id
     *
     * @return
     * @throws NoSuchEntityException
     */
    public function getById(int $id): ProjectInterface;

    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface;

    /**
     * @param ProjectInterface $project
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ProjectInterface $project): bool;

    /**
     * @param int $id
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): bool;
}
