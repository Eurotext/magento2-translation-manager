<?php
declare(strict_types=1);

/** @var $product \Magento\Catalog\Model\Product */

use Eurotext\TranslationManager\Model\Project;

/** @var \Eurotext\TranslationManager\Model\Project $project */
$project = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(Project::class);
$project->isObjectNew(true);
$project->setName('project-' . time());
$project->save();
