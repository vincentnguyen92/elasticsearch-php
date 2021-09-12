<?php

namespace Okxe\Elasticsearch\Managers;

use Okxe\Elasticsearch\Abstracts\AbstractManager;
use Okxe\Elasticsearch\Abstracts\AbstractIndex;

/**
 * Manager for everything index related. Holds a container for
 * used indexes. Also holds basic CRUD operations on those indexes.
 */
final class IndicesManager extends AbstractManager
{
    /**
     * Actions to the ElasticSearch server.
     * 
     * @return mixed
     */
    public function indices()
    {
        return $this->elasticSearcher->getClient()->indices()->getMapping();
    }

    /**
     * @param AbstractIndex $indexName
     */
    public function create(AbstractIndex $index)
    {
        $params = [
            'index' => $index->getInternalName(),
            'body'  => $index->getBody()
        ];

        $this->elasticSearcher->getClient()->indices()->create($params);
    }

    public function createOrUpdate(AbstractIndex $index)
    {
        if ($this->exists($index)) {
            $this->update($index);
        } else {
            $this->create($index);
        }
    }

    public function forceCreateNew(AbstractIndex $index)
    {
        if ($this->exists($index)) {
            $this->delete($index);
            $this->create($index);
        } else {
            $this->create($index);
        }
    }

    /**
     * @param AbstractIndex $index
     * @return bool
     */
    public function exists(AbstractIndex $index)
    {
        $params = [
            'index' => $index->getInternalName()
        ];

        return $this->elasticSearcher->getClient()->indices()->exists($params);
    }

    /**
     * Update the index and all its types. This should be used when wanting to reflect changes
     * in the Index object with the elasticsearch server.
     *
     * @param AbstractIndex $index
     */
    public function update(AbstractIndex $index)
    {
        $params = [
            'index' => $index->getInternalName(),
            'body'  => $index->getMappings()
        ];

        $this->elasticSearcher->getClient()->indices()->putMapping($params);
    }

    /**
     * @param AbstractIndex $indexName
     */
    public function delete(AbstractIndex $index)
    {
        $params = [
            'index' => $index->getInternalName()
        ];

        $this->elasticSearcher->getClient()->indices()->delete($params);
    }
}
