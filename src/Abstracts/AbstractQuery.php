<?php

namespace Okxe\Elasticsearch\Abstracts;

use Okxe\Elasticsearch\ElasticsearchService;
use Okxe\Elasticsearch\Parsers\ResultParser;
use Okxe\Elasticsearch\Parsers\ResultParserPagination;
use Okxe\Elasticsearch\Traits\BodyTrait;
use Okxe\Elasticsearch\Traits\OrderByTrait;

/**
 * Base class for queries.
 */
abstract class AbstractQuery
{
    use BodyTrait, OrderByTrait;

    /**
     * @var Okxe\Elasticsearch\ElasticsearchService
     */
    protected $searcher;

    /**
     * Detect the query enable Pagination or not
     *
     * @var boolean
     */
    protected $pagination = false;

    /**
     * From the result
     *
     * @var integer
     */
    protected $from = 0;

    /**
     * Limit the result
     *
     * @var integer
     */
    protected $size = 10;

    /**
     * Min score for result
     *
     * @var float
     */
    protected $minScore = 0;

    /**
     * Sort with score flag
     *
     * @var bool
     */
    protected $withSortScore = false;

    /**
     * Keeping the result with pagination
     *
     * @var string
     */
    protected $scrollTimeOut = '5m';

    /**
     * Indices on which the query should be executed.
     *
     * @var array
     */
    protected $indices = [];

    /**
     * Setting parser default for Query
     *
     * @var array
     */
    protected $parser = null;

    /**
     * Source fields
     *
     * @var array
     */
    protected $source = ["*"];

    /**
     * Script fields
     *
     * @var array
     */
    protected $scriptFields = [];

    /**
     * Filter operators
     * @var array
     */
    protected $operators = [
        "=",
        "!=",
        ">",
        ">=",
        "<",
        "<=",
        "like",
        "prefix",
        "exists",
    ];

    /**
     * Query bool must
     * @var array
     */
    protected $must = [];

    /**
     * Query bool must not
     * @var array
     */
    protected $must_not = [];

    /**
     * Query bool should
     * @var array
     */
    protected $should = [];

    /**
     * Query bool multi_match
     * @var array
     */
    protected $multi_match = [];

    /**
     * Scroll ID for pagination
     *
     * @var string
     */
    protected $after = "";


    /**
     * @var AbstractResultParser
     */
    protected $resultParser;

    public function __construct()
    {
        $this->searcher = app()->make(ElasticsearchService::class);

        // Default result parser.
        if ($this->parser) { // When exist the property setting parser
            $this->setParser(new $this->parser());
        } else {
            if ($this->pagination) { // The pagination in maintaining...
                $this->setParser(new ResultParserPagination());
            } else {
                $this->setParser(new ResultParser());
            }
        }
    }

    /**
     * Enable for sorting, we don't need to use sorting with score be default
     *
     * @return self
     */
    public function withScoreSorting()
    {
        $this->withSortScore = true;

        return $this;
    }

    /**
     * Set the query where clause
     * @param        $name
     * @param string $operator
     * @param null $value
     * @return $this
     */
    public function where($name, $operator = "=", $value = NULL)
    {
        if (!$this->isOperator($operator)) {
            $value = $operator;
            $operator = "=";
        }

        if ($operator == "=") {
            $this->must[] = ["term" => [$name => $value]];
        }

        if ($operator == ">") {
            $this->must[] = ["range" => [$name => ["gt" => $value]]];
        }

        if ($operator == ">=") {
            $this->must[] = ["range" => [$name => ["gte" => $value]]];
        }

        if ($operator == "<") {
            $this->must[] = ["range" => [$name => ["lt" => $value]]];
        }

        if ($operator == "<=") {
            $this->must[] = ["range" => [$name => ["lte" => $value]]];
        }

        if ($operator == "like") {
            $this->must[] = ["match" => [$name => $value]];
        }

        if ($operator == "prefix") {
            $this->must[] = ["match_phrase_prefix" => [
                $name => [
                    'query' => $value,
                    'max_expansions' => 50
                ]
            ]];
        }

        if ($operator == 'exists') {
            $this->must[] = [
                'exists' => [
                    'field' => $name
                ],
            ];
        }

        return $this;
    }

    /**
     * Set the query inverse where clause
     * @param        $name
     * @param string $operator
     * @param null $value
     * @return $this
     */
    public function whereNot($name, $operator = "=", $value = NULL)
    {
        if (!$this->isOperator($operator)) {
            $value = $operator;
            $operator = "=";
        }

        if ($operator == "=") {
            $this->must_not[] = ["term" => [$name => $value]];
        }

        if ($operator == ">") {
            $this->must_not[] = ["range" => [$name => ["gt" => $value]]];
        }

        if ($operator == ">=") {
            $this->must_not[] = ["range" => [$name => ["gte" => $value]]];
        }

        if ($operator == "<") {
            $this->must_not[] = ["range" => [$name => ["lt" => $value]]];
        }

        if ($operator == "<=") {
            $this->must_not[] = ["range" => [$name => ["lte" => $value]]];
        }

        if ($operator == "like") {
            $this->must_not[] = ["match" => [$name => $value]];
        }

        if ($operator == "prefix") {
            $this->must_not[] = ["match_phrase_prefix" => [
                $name => [
                    'query' => $value,
                    'max_expansions' => 50
                ]
            ]];
        }

        if ($operator == 'exists') {
            $this->must_not[] = [
                'exists' => [
                    'field' => $name
                ],
            ];
        }

        return $this;
    }

    /**
     * Set the query where between clause
     * @param $name
     * @param $firstVal
     * @param $lastVal
     * @return $this
     */
    public function whereBetween($name, $firstVal, $lastVal = null, $boost = 1.0)
    {
        if (is_array($firstVal) && count($firstVal) == 2) {
            $lastVal  = $firstVal[1];
            $firstVal = $firstVal[0];
        }

        $this->must[] = [
            "range" => [
                $name => [
                    "gte" => empty($firstVal) ? null : $firstVal,
                    "lte" => empty($lastVal) ? null : $lastVal,
                    "boost" => $boost
                ]
            ]
        ];

        return $this;
    }

    /**
     * Set the query where not between clause
     * @param $name
     * @param $first_value
     * @param $last_value
     * @return $this
     */
    public function whereNotBetween($name, $first_value, $last_value = null)
    {
        if (is_array($first_value) && count($first_value) == 2) {
            $last_value = $first_value[1];
            $first_value = $first_value[0];
        }

        $this->must_not[] = ["range" => [$name => ["gte" => $first_value,   "lte" => $last_value]]];

        return $this;
    }

    /**
     * Set the query where in clause
     * @param       $name
     * @param array $value
     * @return $this
     */
    public function whereIn($name, $value = [])
    {
        $this->must[] = ["terms" => [$name => $value]];

        return $this;
    }

    /**
     * Set the query where not in clause
     * @param       $name
     * @param array $value
     * @return $this
     */
    public function whereNotIn($name, $value = [])
    {
        $this->must_not[] = ["terms" => [$name => $value]];

        return $this;
    }

    /**
     * Set the Query OR clasue
     */
    public function whereOr($name, $operator = "=", $value = NULL)
    {
        if (!$this->isOperator($operator)) {
            $value = $operator;
            $operator = "=";
        }

        if ($operator == "=") {
            $this->should[] = ["term" => [$name => $value]];
        }

        if ($operator == ">") {
            $this->should[] = ["range" => [$name => ["gt" => $value]]];
        }

        if ($operator == ">=") {
            $this->should[] = ["range" => [$name => ["gte" => $value]]];
        }

        if ($operator == "<") {
            $this->should[] = ["range" => [$name => ["lt" => $value]]];
        }

        if ($operator == "<=") {
            $this->should[] = ["range" => [$name => ["lte" => $value]]];
        }

        if ($operator == "like") {
            $this->should[] = ["match" => [$name => $value]];
        }

        return $this;
    }

    /**
     * Scroll pagination
     *
     * @param string $scrollId
     * @return self
     */
    public function after(string $scrollId)
    {
        $this->after = $scrollId;

        return $this;
    }

    /**
     * Default pagination
     *
     * @param int $page default 1
     * @return self
     */
    public function from(int $page = 1)
    {
        $page = $page > 0 ? $page : 1;

        $this->from = ($page - 1) * $this->size;

        return $this;
    }

    /**
     * Default pagination
     *
     * @param int $size default 10
     * @return self
     */
    public function size(int $size = 10)
    {
        $size = $size > 0 ? $size : 1;

        $this->size = $size;

        return $this;
    }

    /**
     * Set min_score for Query
     *
     * @param float $minScore
     * @return self
     */
    public function minScore(float $minScore)
    {
        $this->minScore = $minScore;

        return $this;
    }

    /**
     * Set source fields
     *
     * @param array $src default ["*"]
     * @return self
     */
    public function source(array $src)
    {
        $this->source = $src;

        return $this;
    }

    /**
     * Set script fields
     *
     * @param string $fieldName
     * @param string $script
     * @param string $lang
     * @return self
     */
    public function scriptFields(string $fieldName, string $source = '', string $lang = 'painless')
    {
        $this->scriptFields[$fieldName] = [
            'script' => [
                'lang' => $lang,
                'source' => $source
            ]
        ];

        return $this;
    }

    /**
     * @param string $query
     * @param array $fields
     * @param string $type best_fields|most_fields|phrase_prefix
     * @return self
     */
    public function multi_match(string $query = "", array $fields = [], string $type = "most_fields", array $options = [])
    {
        $this->multi_match[] = [
            "multi_match" => array_merge(
                [
                    "query" => $query,
                    "type" => $type,
                    "fields" => $fields,
                ],
                $options
            )
        ];

        return $this;
    }

    /**
     * Build the query by adding all chunks together.
     *
     * @return array
     */
    protected function buildQuery()
    {
        $query = [];
        $query['index'] = $this->buildIndices();

        if (count($this->source)) {
            $query["body"]["_source"] = $this->source;
        }

        if (count($this->scriptFields)) {
            $query["body"]["script_fields"] = $this->scriptFields;
        }

        // Score sorting
        if ($this->withSortScore) {
            $query["body"]["sort"][] = "_score";
            $query["body"]["min_score"] = $this->minScore;
            $query["body"]["track_scores"] = true;
        }

        if (!empty($this->sort)) {
            $query["body"]["sort"][] = $this->sort;
        }

        if (count($this->must)) {
            $query["body"]["query"]["bool"]["filter"]["bool"]["must"] = $this->must;
        }

        if (count($this->must_not)) {
            $query["body"]["query"]["bool"]["filter"]["bool"]["must_not"] = $this->must_not;
        }

        if (count($this->should)) {
            $query["body"]["query"]["bool"]["filter"]["bool"]["should"] = $this->should;
        }

        if (count($this->multi_match)) {
            $query["body"]["query"]["bool"]["must"] = $this->multi_match;
        }

        if ($this->pagination) {
            $query["scroll"] = $this->scrollTimeOut;
        }

        if ($this->size) {
            $query["size"] = $this->size;
        }

        if ($this->from) {
            $query["from"] = $this->from;
        }

        // $query["explain"] = true;
        // dd($query);

        return $query;
    }

    /**
     * @return mixed
     */
    public function getResultParser()
    {
        return $this->resultParser;
    }

    /**
     * Indices we are searching in.
     *
     * @return array
     */
    public function getIndices()
    {
        return $this->indices;
    }

    /**
     * Get the query after being build.
     * This is what will be sent to the elasticsearch SDK.
     *
     * @return array
     */
    public function getRawQuery()
    {
        return $this->buildQuery();
    }

    /**
     * @param AbstractResultParser $resultParser
     */
    public function setParser(AbstractResultParser $resultParser)
    {
        $this->resultParser = $resultParser;
    }

    /**
     * Build and execute the query.
     *
     * @return AbstractResultParser
     */
    public function run()
    {
        $query = $this->buildQuery();

        // Execute the query.
        if ($this->pagination && !empty($this->after)) {
            $rawResults = $this->searcher->getClient()->scroll([
                'scroll_id' => $this->after,
                'scroll' => $this->scrollTimeOut,
            ]);
        } else {
            $rawResults = $this->searcher->getClient()->search($query);
        }

        // Pass response to the class that will do something with it.
        $resultParser = $this->getResultParser();
        $resultParser->setRawResults($rawResults);
        // dd($rawResults);

        return $resultParser;
    }

    /**
     * check if it's a valid operator from defined operator array
     * @param $string
     * @return bool
     */
    protected function isOperator($string)
    {

        if (in_array($string, $this->operators)) {
            return true;
        }

        return false;
    }

    /**
     * Get listing the index name from Index Instance
     *
     * @return string
     */
    private function buildIndices(): string
    {
        if (count($this->indices)) {
            $_indices = [];
            foreach ($this->indices as $index) {
                $_indices[] = (new $index)->getInternalName();
            }
            return implode(',', array_values($_indices));
        } else {
            return '_all';
        }
    }
}
