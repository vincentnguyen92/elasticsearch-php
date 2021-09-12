<?php

namespace Okxe\Elasticsearch\Abstracts;

/**
 * Base class for managers.
 */
abstract class AbstractManager
{
    /**
     * @var ElasticsearchService
     */
    protected $elasticSearcher;

    /**
     * @param ElasticsearchService $elasticSearcher
     */
    public function __construct(\Okxe\Elasticsearch\ElasticsearchService $elasticSearcher)
    {
        $this->elasticSearcher = $elasticSearcher;
    }
}
