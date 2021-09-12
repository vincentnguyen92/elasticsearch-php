<?php

namespace Okxe\Elasticsearch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class CommandController extends Controller
{
    /**
     * API: /api/index-search-popular-keyword
     */
    public function indexSearchPopularKeyword()
    {
        Artisan::queue('index:search-popular-keyword');
        return "Please waiting....And check in Slack channel #backend-log-es-sync";
    }

    /**
     * API: /api/index-search-user-keyword
     */
    public function indexSearchUserKeyword()
    {
        Artisan::queue('index:search-user-keyword');
        return "Please waiting....And check in Slack channel #backend-log-es-sync";
    }

    /**
     * API: /api/index-product
     */
    public function indexProduct()
    {
        Artisan::queue('index:product');
        return "Please waiting....And check in Slack channel #backend-log-es-sync";
    }

    /**
     * API: /api/index-store
     */
    public function indexStore()
    {
        Artisan::queue('index:store');
        return "Please waiting....And check in Slack channel #backend-log-es-sync";
    }
}
