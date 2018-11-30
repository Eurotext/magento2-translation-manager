<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Setup\Service;

use Magento\Framework\DB\Ddl\Table as DbDdlTable;
use Magento\Framework\Setup\SchemaSetupInterface;

interface AddEntityTableColumnsInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param DbDdlTable $table
     *
     * @return DbDdlTable
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup, DbDdlTable $table): DbDdlTable;
}