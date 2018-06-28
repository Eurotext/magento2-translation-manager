<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Setup;

use Eurotext\TranslationManager\Setup\Service\CreateProjectProductSchema;
use Eurotext\TranslationManager\Setup\Service\CreateProjectSchema;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Eurotext\TranslationManager\Setup\Service\CreateProjectSchema
     */
    private $createProjectSchema;

    /**
     * @var \Eurotext\TranslationManager\Setup\Service\CreateProjectProductSchema
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
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->createProjectSchema->execute($setup);
        $this->createProjectProductSchema->execute($setup);
    }
}
