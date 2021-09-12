<?php

namespace Okxe\Elasticsearch;

use Elasticsearch\Client;
use Okxe\Elasticsearch\Managers\DocumentsManager;
use Okxe\Elasticsearch\Managers\IndicesManager;

/**
 * Elasticsearch Adapter for query to elasticsearch database
 * 
 * @author Vincent Nguyen <vannguyen@okxe.vn>
 */
class ElasticsearchService implements ElasticsearchContract
{
    /**
     * Elasticsearch binding at ElasticsearchServiceProvider
     *
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @var Okxe\Elasticsearch\Managers\DocumentsManager
     */
    private $documentsManager;

    /**
     * @var Okxe\Elasticsearch\Managers\IndicesManager
     */
    private $indicesManager;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return DocumentsManager
     */
    public function documentsManager(): DocumentsManager
    {
        if (!$this->documentsManager) {
            $this->documentsManager = new DocumentsManager($this);
        }

        return $this->documentsManager;
    }

    /**
     * @return IndicesManager
     */
    public function indicesManager(): IndicesManager
    {
        if (!$this->indicesManager) {
            $this->indicesManager = new IndicesManager($this);
        }

        return $this->indicesManager;
    }

    /**
     * Check cluster healthy
     * 
     * @return bool
     */
    public function isHealthy()
    {
        $info = $this->getClient()->cluster()->health();

        return $info['status'] == 'green';
    }
}
