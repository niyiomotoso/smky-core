<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use App\Traits\ReturnsJsonResponses;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
class UrlController extends BaseController
{
    private UrlService $urlService;
    use ReturnsJsonResponses;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;

    }

    public function add(Request $request)
    {
        $rules = [
            "url" => "required",
        ];

        $url = $request->get('url');
        $alias = $request->get('alias');
        $customPath = $request->get('customPath');
        $data = $this->urlService->add(compact('url', 'alias', 'customPath'));

        return $this->success_response($data, "success");
    }

    public function getURLByPath($path)
    {
        if (empty($path))
            return$this->error_response("path is required", "path is required", 400);

        $url  = $this->urlService->getURLByPath($path);
        return $this->success_response($url, "success");
    }

    public function getURLByOrigin($origin)
    {
        if (empty($origin))
            return$this->error_response("origin is required", "origin is required", 400);

        $url  = $this->urlService->getURLByOrigin($origin);
        return $this->success_response($url, "success");
    }

    public function reloadCache()
    {
        $urlCount  = $this->urlService->reloadCache();
        return $this->success_response($urlCount, "success");
    }

    public function getAllUrlsFromDB()
    {
        $urlCount  = $this->urlService->getAllURLs();
        return $this->success_response($urlCount, "success");
    }
}
