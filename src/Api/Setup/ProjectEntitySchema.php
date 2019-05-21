<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Api\Setup;

interface ProjectEntitySchema
{
    const ID         = 'id';
    const EXT_ID     = 'ext_id';
    const PROJECT_ID = 'project_id';
    const ENTITY_ID  = 'entity_id';
    const STATUS     = 'status';
    const LAST_ERROR = 'last_error';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
