<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Setup\Service;

use Eurotext\TranslationManager\Api\Setup\ProjectEntitySchema;
use Magento\Framework\DB\Ddl\Table as DbDdlTable;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddEntityTableColumns implements AddEntityTableColumnsInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param DbDdlTable $table
     *
     * @return DbDdlTable
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup, DbDdlTable $table): DbDdlTable
    {
        $table->addColumn(
            ProjectEntitySchema::ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['primary' => true, 'unsigned' => true, 'nullable' => false, 'auto_increment' => true,],
            'Project Product ID'
        );
        $table->addColumn(
            ProjectEntitySchema::EXT_ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'External ID provided by Eurotext'
        );
        $table->addColumn(
            ProjectEntitySchema::PROJECT_ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false,],
            'Project ID'
        );
        $table->addColumn(
            ProjectEntitySchema::ENTITY_ID,
            DbDdlTable::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false,],
            'Attribute ID'
        );
        $table->addColumn(
            ProjectEntitySchema::STATUS,
            DbDdlTable::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Status'
        );
        $table->addColumn(
            ProjectEntitySchema::LAST_ERROR,
            DbDdlTable::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Last error details and message'
        );
        $table->addColumn(
            ProjectEntitySchema::CREATED_AT,
            DbDdlTable::TYPE_TIMESTAMP,
            null,
            [],
            'Created at'
        );
        $table->addColumn(
            ProjectEntitySchema::UPDATED_AT,
            DbDdlTable::TYPE_TIMESTAMP,
            null,
            ['default' => DbDdlTable::TIMESTAMP_INIT_UPDATE],
            'Last Update'
        );

        $idxName = $setup->getIdxName($table->getName(), [ProjectEntitySchema::EXT_ID]);
        $table->addIndex($idxName, [ProjectEntitySchema::EXT_ID]);

        $idxName = $setup->getIdxName($table->getName(), [ProjectEntitySchema::PROJECT_ID]);
        $table->addIndex($idxName, [ProjectEntitySchema::PROJECT_ID]);

        $idxName = $setup->getIdxName($table->getName(), [ProjectEntitySchema::ENTITY_ID]);
        $table->addIndex($idxName, [ProjectEntitySchema::ENTITY_ID]);

        return $table;
    }
}
