<?php

namespace Okxe\Elasticsearch;

use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * The fully config module's PATH
     *
     * @var string
     */
    private $configPath;

    /**
     * Initial the class
     *
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->configPath = dirname(__DIR__) . '/src/elasticsearch.php';
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadRoutesFrom(__DIR__ . '/route.php');
        $this->mergeConfigFrom(
            $this->configPath,
            'elasticsearch'
        );
        $this->bindSearchClient();
        $this->bindFacade();
    }

    /**
     * Create the Elasticsearch Client with connection when inject the Client class
     *
     * @return void
     */
    private function bindSearchClient()
    {
        $this->app->singleton(\Elasticsearch\Client::class, function () {
            $client = \Elasticsearch\ClientBuilder::create()
                ->setHosts(config('elasticsearch.config.hosts'))
                ->setBasicAuthentication(config('elasticsearch.config.username'), config('elasticsearch.config.password'))
                ->setLogger(\Illuminate\Support\Facades\Log::channel('slack_elasticsearch'))
                ->build();
            $params = ['client' => config('elasticsearch.config.client')];
            if ($client->ping($params)) { # Check connection available
                return $client;
            }
        });
    }

    /**
     * Register facade
     *
     * @return void
     */
    private function bindFacade()
    {
        $this->app->bind('elasticsearch', function ($app) {
            return new ElasticsearchService(
                $app->make(\Elasticsearch\Client::class)
            );
        });
    }
}
