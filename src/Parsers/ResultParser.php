<?php

namespace Okxe\Elasticsearch\Parsers;

use Okxe\Elasticsearch\Abstracts\AbstractResultParser;
use App\Traits\ApiResponser;

/**
 * @package ElasticSearcher\Parsers
 */
class ResultParser extends AbstractResultParser
{
    use ApiResponser;

    public function getResults()
    {
        return $this->successResponse($this->getSource());
    }
}
