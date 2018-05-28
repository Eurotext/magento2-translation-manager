<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Setup;

/**
 * ProjectSchema
 */
class ProjectSchema
{
    public const TABLE_NAME = 'eurotext_project';

    public const ID = 'id';
    public const EXT_ID = 'ext_id';
    public const NAME = 'name';
    public const CODE = 'code';
    public const STOREVIEW_SRC = 'storeview_src';
    public const STOREVIEW_DST = 'storeview_dst';
    public const STATUS = 'status';
    public const CUSTOMER_COMMENT = 'customer_comment';
    public const LAST_ERROR = 'last_error';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
}