<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;
use Elastic\Transport\Exception\NoNodeAvailableException;
use JetBrains\PhpStorm\NoReturn;

class ElasticSearchController extends Controller
{
    protected Client $client;
    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()->setElasticCloudId(env('ELASTICSEARCH_CLOUD_ID'))->setApiKey(env('ELASTICSEARCH_API_KEY'))->build();
        $this->client->setAsync(true);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function getBooks(): void
    {
        $params = [
            'index' => 'my_index',
            'body'  => [
                'query' => [
                    'match' => [
                        'testField' => 'abc'
                    ]
                ]
            ]
        ];

        $response = $this->client->search($params);
        dump($response);
    }
}
