<?php
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Setup;

use Eurotext\TranslationManager\Setup\Service\CreateProjectProductSchema;
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

    /**
     * @var CreateProjectProductSchema
     */
    private $createProjectProductSchema;

    public function __construct(
        CreateProjectSchema $createProjectSchema,
        CreateProjectProductSchema $createProjectProductSchema
    ) {
        $this->createProjectSchema = $createProjectSchema;
        $this->createProjectProductSchema = $createProjectProductSchema;
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
        $this->createProjectProductSchema->execute($setup);
    }
}