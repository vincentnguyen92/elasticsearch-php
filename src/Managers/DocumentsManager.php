<?php

namespace Okxe\Elasticsearch\Managers;

use Okxe\Elasticsearch\Abstracts\AbstractManager;
use Okxe\Elasticsearch\Abstracts\AbstractIndex;

/**
 * Manager for everything document related. Holds basic CRUD operations on documents.
 */
final class DocumentsManager extends AbstractManager
{
    /**
     * Create a document.
     * 
     * @param AbstractIndex $index
     * @param string $type
     * @param array  $data
     * @return array
     */
    public function index(AbstractIndex $index, array $data)
    {
        $params = [
            'index' => $index->getInternalName(),
            'body'  => $data
        ];

        // If an ID exists in the data set, use it, otherwise let elasticsearch generate one.
        if (array_key_exists('id', $data)) {
            $params['id'] = $data['id'];
        }

        return $this->elasticSearcher->getClient()->index($params);
    }

    /**
     * Index a set of documents.
     *
     * @param AbstractIndex $index
     * @param array  $data
     */
    public function bulkIndex(AbstractIndex $index, array $data)
    {
        $params = ['body' => []];

        foreach ($data as $item) {
            $header = [
                '_index' => $index->getInternalName()
            ];

            if (array_key_exists('id', $item)) {
                $header['_id'] = $item['id'];
            }

            // The bulk operation expects two JSON objects for each item
            // the first one should describe the operation, index, type
            // and ID. The later one is the document body.
            $params['body'][] = ['index' => $header];
            $params['body'][] = $item;
        }

        $this->elasticSearcher->getClient()->bulk($params);
    }

    /**
     * @param AbstractIndex $index
     * @param string $id
     * @return array
     */
    public function delete(AbstractIndex $index, string $id)
    {
        $params = [
            'index' => $index->getInternalName(),
            'id'    => $id
        ];

        return $this->elasticSearcher->getClient()->delete($params);
    }

    /**
     * Partial updating of an existing document.
     *
     * @param AbstractIndex $indexName
     * @param string $id
     * @param array  $data
     * @return array
     */
    public function update(AbstractIndex $index, string $id, array $data)
    {
        $params = [
            'index' => $index->getInternalName(),
            'id'    => $id,
            'body'  => ['doc' => $data]
        ];

        return $this->elasticSearcher->getClient()->update($params);
    }

    /**
     * @param AbstractIndex $indexName
     * @param string $id
     * @return bool
     */
    public function exists(AbstractIndex $index, string $id)
    {
        $params = [
            'index' => $index->getInternalName(),
            'id'    => $id,
        ];

        return $this->elasticSearcher->getClient()->exists($params);
    }

    /**
     * Update a document. Create it if it doesn't exist.
     * 
     * @param AbstractIndex $index
     * @param string $id
     * @param array $data
     * @return array
     */
    public function updateOrIndex(AbstractIndex $index, string $id, array $data)
    {
        if ($this->exists($index, $id)) {
            return $this->update($index, $id, $data);
        } else {
            return $this->index($index, $data);
        }
    }

    /**
     *
     * @param AbstractIndex $index
     * @param string $id
     * @return array
     */
    public function get(AbstractIndex $index, $id)
    {
        $params = [
            'index' => $index->getInternalName(),
            'id'    => $id,
        ];

        return $this->elasticSearcher->getClient()->get($params);
    }

    /**
     * @param \Okxe\Elasticsearch\Abstracts\AbstractQuery $query
     * @return array
     */
    public function deleteByQuery(\Okxe\Elasticsearch\Abstracts\AbstractQuery $query)
    {
        return $this->elasticSearcher->getClient()->deleteByQuery($query->getRawQuery());
    }
}
