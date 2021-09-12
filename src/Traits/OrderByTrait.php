<?php

namespace Okxe\Elasticsearch\Traits;

trait OrderByTrait
{
    /**
     * Body of the query to execute.
     * We should calculator the score for better searching
     *
     * @var array
     */
    public $sort = null;

    /**
     * @param array $body
     */
    public function orderBy(string $field, string $term)
    {
        $this->sort[$field] = $term;

        return $this;
    }
}
