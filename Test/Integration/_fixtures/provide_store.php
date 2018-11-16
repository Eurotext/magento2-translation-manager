<?php
declare(strict_types=1);

use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap as BootstrapHelper;

$code = 'store_dest';

/** @var Store $store */
$store = BootstrapHelper::getObjectManager()->create(Store::class);
$store->isObjectNew(true);
$store->setName($code);
$store->setCode($code);
$store->setWebsiteId(1);
$store->setStoreGroupId(1);
$store->save();
