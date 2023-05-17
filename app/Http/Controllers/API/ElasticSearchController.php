<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;
use Illuminate\Http\Request;

class ElasticSearchController extends Controller
{
    protected Client $client;
    private ApiResponse $apiResponse;

    /**
     * @throws AuthenticationException
     */
    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;

        $this->client = ClientBuilder::create()->setElasticCloudId(env('ELASTICSEARCH_CLOUD_ID'))->setApiKey(env('ELASTICSEARCH_API_KEY'))->build();
        $this->client->setAsync(true);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function getBooks(Request $request)
    {
        $q = $request->get('q');
        if ($q) {
            $response = $this->client->search([
                'index' => 'books',
                'body'  => [
                    'query' => [
                        'multi_match' => [
                            'query' => $q,
                            'fields' => [
                                'title',
                                'author'
                            ]
                        ]
                    ]
                ]
            ]);
            $bookIds = array_column($response['hits']['hits'], '_id');
            $result = Book::query()->findMany($bookIds);
            return $this->apiResponse->publicSendResponse(200, "Books By Keywords", count($result),$result);
        } else {
            $result = Book::all();
            return $this->apiResponse->publicSendResponse(200, "All Books", count($result),$result);
        }
    }
}
