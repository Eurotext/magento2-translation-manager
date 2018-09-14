<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Setup;

use Eurotext\TranslationManager\Setup\Service\CreateProjectSchema;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * UpdateSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var CreateProjectSchema
     */
    private $createProjectSchema;

    public function __construct(
        CreateProjectSchema $createProjectSchema
    ) {
        $this->createProjectSchema = $createProjectSchema;
    }

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->createProjectSchema->execute($setup);
    }
}
