<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
class UrlController extends BaseController
{
    private UrlService $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;

    }

    public function add(Request $request)
    {
        $rules = [
            "url" => "required|url",
        ];

        $messages = [];
        $this->validate($request, $rules, $messages);
        $url = $request->get('url');
        $alias = $request->get('alias');
        $customPath = $request->get('customPath');


        return $this->urlService->add(compact('url', 'alias', 'customPath'));
    }

    public function getURL(Request $request)
    {

    }
}
