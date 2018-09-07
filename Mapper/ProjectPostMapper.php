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
use Eurotext\RestApiClient\Request\ProjectDataRequest;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;

class ProjectPostMapper
{
    public function map(ProjectInterface $project): ProjectDataRequest
    {
        $data = new ProjectData($project->getCustomerComment());

        $request = new ProjectDataRequest($project->getName(), $data, ProjectTypeEnum::QUOTE());

        return $request;
    }

}