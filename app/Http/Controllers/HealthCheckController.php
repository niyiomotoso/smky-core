<?php

namespace App\Http\Controllers;

use App\Traits\ReturnsJsonResponses;
use Illuminate\Routing\Controller as BaseController;
class HealthCheckController extends BaseController
{
    use ReturnsJsonResponses;

    public function __construct()
    {

    }

    public function healthcheck()
    {
        return $this->success_response("ok", "success");
    }
}
