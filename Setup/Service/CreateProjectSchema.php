<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Setup\Service;

use Eurotext\TranslationManager\Setup\ProjectSchema;
use Magento\Framework\DB\Ddl\Table as DbDdlTable;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * CreateProjectSchema
 */
class CreateProjectSchema
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup): void
    {
        $connection = $setup->getConnection();

        $tableName = $setup->getTable(ProjectSchema::TABLE_NAME);
        $table = $connection->newTable($tableName);
        $table->addColumn(
            ProjectSchema::ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['primary' => true, 'unsigned' => true, 'nullable' => false, 'auto_increment' => true,],
            'Project ID'
        );
        $table->addColumn(
            ProjectSchema::EXT_ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'External ID provided by Eurotext'
        );
        $table->addColumn(
            ProjectSchema::NAME,
            DbDdlTable::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Project name'
        );
        $table->addColumn(
            ProjectSchema::CODE,
            DbDdlTable::TYPE_TEXT,
            255,
            [],
            'Unique project identifier'
        );
        $table->addColumn(
            ProjectSchema::STOREVIEW_SRC,
            DbDdlTable::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => -1],
            'Source Storeview'
        );
        $table->addColumn(
            ProjectSchema::STOREVIEW_DST,
            DbDdlTable::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => -1],
            'Destination Storeview'
        );
        $table->addColumn(
            ProjectSchema::STATUS,
            DbDdlTable::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 0],
            'Status'
        );
        $table->addColumn(
            ProjectSchema::CUSTOMER_COMMENT,
            DbDdlTable::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Customer specific comment'
        );
        $table->addColumn(
            ProjectSchema::LAST_ERROR,
            DbDdlTable::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Last error details and message'
        );
        $table->addColumn(
            ProjectSchema::CREATED_AT,
            DbDdlTable::TYPE_TIMESTAMP,
            null,
            [],
            'Created at'
        );
        $table->addColumn(
            ProjectSchema::UPDATED_AT,
            DbDdlTable::TYPE_TIMESTAMP,
            null,
            ['default' => DbDdlTable::TIMESTAMP_INIT_UPDATE],
            'Last Update'
        );

        $idxName = $setup->getIdxName($tableName, ['ext_id']);
        $table->addIndex($idxName, ['ext_id']);

        $connection->createTable($table);

    }
}