<?php

namespace Okxe\Elasticsearch\Traits;

/**
 * Shortcut to adding pagination to a Query.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-from-size.html
 */
trait PaginatedTrait
{
    /**
     * @param int $page
     * @param int $perPage
     *
     * @return self
     */
    public function paginate(int $page, int $perPage = 30)
    {
        $this->set('from', max(0, $perPage * ($page - 1)));
        $this->set('size', $perPage);

        return $this;
    }
}
