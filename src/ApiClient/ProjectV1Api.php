<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\ApiClient;

use GuzzleHttp\ClientInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectV1Api extends \Eurotext\RestApiClient\Api\ProjectV1Api
{
    public function __construct(
        ConfigurationFactory $configFactory,
        ClientInterface $client = null
    ) {
        $config = $configFactory->create();

        parent::__construct($config, $client);
    }
}