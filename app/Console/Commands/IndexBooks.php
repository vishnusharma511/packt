<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use Exception;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;

class IndexBooks extends Command
{

    protected Client $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:books';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Books Indexing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->client = ClientBuilder::create()->setElasticCloudId(env('ELASTICSEARCH_CLOUD_ID'))->setApiKey(env('ELASTICSEARCH_API_KEY'))->build();
        $this->client->setAsync(true);

        $books = Book::all();

        foreach ($books as $book) {
            try {
                $this->client->index([
                    'id' => $book->id,
                    'index' => 'books',
                    'body' => [
                        'title' => $book->title,
                        'author' => $book->author
                    ]
                ]);
            } catch (Exception $e) {
                $this->info($e->getMessage());
            }
        }

        $this->info("Books were successfully indexed");
    }
}
