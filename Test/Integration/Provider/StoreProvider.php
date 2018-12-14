<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Test\Integration\Provider;

use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

class StoreProvider
{
    public static function createStore(string $code): Store
    {
        $store = Bootstrap::getObjectManager()->create(Store::class);
        $store->isObjectNew(true);
        $store->setName($code);
        $store->setCode($code);
        $store->setWebsiteId(1);
        $store->setStoreGroupId(1);
        $store->save();

        return $store;
    }
}
