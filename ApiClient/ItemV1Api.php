<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\ApiClient;

use GuzzleHttp\ClientInterface;

class ItemV1Api extends \Eurotext\RestApiClient\Api\Project\ItemV1Api
{
    public function __construct(
        ConfigurationFactory $configFactory,
        ClientInterface $client = null
    ) {
        $config = $configFactory->create();
        
        parent::__construct($config, $client);
    }
}