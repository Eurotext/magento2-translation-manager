<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Setup\Service;

use Eurotext\TranslationManager\Setup\EntitySchema\ProjectProductSchema;
use Magento\Framework\DB\Ddl\Table as DbDdlTable;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateProjectProductSchema
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();

        $tableName = $setup->getTable(ProjectProductSchema::TABLE_NAME);
        $table = $connection->newTable($tableName);
        $table->addColumn(
            ProjectProductSchema::ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['primary' => true, 'unsigned' => true, 'nullable' => false, 'auto_increment' => true,],
            'Project Product ID'
        );
        $table->addColumn(
            ProjectProductSchema::EXT_ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'External ID provided by Eurotext'
        );
        $table->addColumn(
            ProjectProductSchema::PROJECT_ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false,],
            'Project ID'
        );
        $table->addColumn(
            ProjectProductSchema::PRODUCT_ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false,],
            'Product ID'
        );
        $table->addColumn(
            ProjectProductSchema::LAST_ERROR,
            DbDdlTable::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Last error details and message'
        );
        $table->addColumn(
            ProjectProductSchema::CREATED_AT,
            DbDdlTable::TYPE_TIMESTAMP,
            null,
            [],
            'Created at'
        );
        $table->addColumn(
            ProjectProductSchema::UPDATED_AT,
            DbDdlTable::TYPE_TIMESTAMP,
            null,
            ['default' => DbDdlTable::TIMESTAMP_INIT_UPDATE],
            'Last Update'
        );

        $idxName = $setup->getIdxName($tableName, [ProjectProductSchema::EXT_ID]);
        $table->addIndex($idxName, [ProjectProductSchema::EXT_ID]);

        $idxName = $setup->getIdxName($tableName, [ProjectProductSchema::PROJECT_ID]);
        $table->addIndex($idxName, [ProjectProductSchema::PROJECT_ID]);

        $idxName = $setup->getIdxName($tableName, [ProjectProductSchema::PRODUCT_ID]);
        $table->addIndex($idxName, [ProjectProductSchema::PRODUCT_ID]);

        $connection->createTable($table);

    }
}
