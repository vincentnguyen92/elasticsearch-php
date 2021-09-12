<?php

namespace Okxe\Elasticsearch\Parsers;

use Okxe\Elasticsearch\Abstracts\AbstractResultParser;

/**
 * @package ElasticSearcher\Parsers
 */
class ResultParserPagination extends AbstractResultParser
{
    public function getResults()
    {
        return [
            "total" => $this->get('hits.total.value'),
            "next_id" => $this->get('_scroll_id'),
            "data" => $this->getSource(),
        ];
    }
}
