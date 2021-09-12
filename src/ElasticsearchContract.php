<?php

namespace Okxe\Elasticsearch;

interface ElasticsearchContract
{
    public function getClient(): \Elasticsearch\Client;
    public function indicesManager(): \Okxe\Elasticsearch\Managers\indicesManager;
    public function documentsManager(): \Okxe\Elasticsearch\Managers\DocumentsManager;
    public function isHealthy();
}
