<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Service;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Eurotext\TranslationManager\Exception\InvalidRequestException;
use Eurotext\TranslationManager\Exception\PersistanceException;
use Magento\Framework\App\Request\Http as HttpRequest;

interface SaveProjectServiceInterface
{
    /**
     * Saves a Project based on a Magento App Http Request
     *
     * @param HttpRequest $httpRequest
     *
     * @return ProjectInterface
     * @throws PersistanceException
     * @throws InvalidRequestException
     */
    public function saveByRequest(HttpRequest $httpRequest): ProjectInterface;
}