<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Mapper;

use Eurotext\RestApiClient\Enum\ProjectTypeEnum;
use Eurotext\RestApiClient\Request\Data\ProjectData;
use Eurotext\RestApiClient\Request\ProjectPostRequest;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;

class ProjectPostMapper
{
    public function map(ProjectInterface $project): ProjectPostRequest
    {
        $data = new ProjectData($project->getCustomerComment());

        $request = new ProjectPostRequest($project->getName(), $data, ProjectTypeEnum::QUOTE());

        return $request;
    }

}