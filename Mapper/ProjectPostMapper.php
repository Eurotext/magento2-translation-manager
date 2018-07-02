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
        $request = new ProjectDataRequest();

        $request->setName($project->getName());
        $request->setType(ProjectTypeEnum::QUOTE());

        $data = new ProjectData();
        $data->setDescription($project->getCustomerComment());
        $request->setData($data);

        return $request;
    }

}