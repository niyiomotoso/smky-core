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

        $messages = [];
        $validated = $request->validate($rules);
        $url = $request->get('url');
        $alias = $request->get('alias');
        $customPath = $request->get('customPath');

        $data = $this->urlService->add(compact('url', 'alias', 'customPath'));

        return $this->success_response($data, "success");
    }

    public function getURL($path)
    {
        if (empty($path))
            return$this->error_response("path is required", "path is required", 400);

        $url  = $this->urlService->getURLByPath($path);
        return $this->success_response($url, "success");
    }
}
