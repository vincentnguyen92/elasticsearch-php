<?php

use Illuminate\Support\Facades\Route;

/**
 * This routes using for running directly the Command through calling the APIs
 */
Route::group([
    'middleware' => 'api',
    'prefix' => 'api',
    'namespace' => 'Okxe\Elasticsearch'
], function () {
    Route::get('test', function () {
        $searcher = app()->make(Okxe\Elasticsearch\ElasticsearchService::class);
        dd($searcher->isHealthy());
    });
    Route::get('index-search-popular-keyword', 'CommandController@indexSearchPopularKeyword');

    Route::get('index-search-user-keyword', 'CommandController@indexSearchUserKeyword');

    Route::get('index-product', 'CommandController@indexProduct');

    Route::get('index-store', 'CommandController@indexStore');
});
