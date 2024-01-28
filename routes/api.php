<?php

use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$router->group(['prefix'=>'v1/url'], function() use ($router) {
    $router->post('/create',  [UrlController::class, 'add']);
    $router->get("/path/{path}", [UrlController::class, 'getURLByPath']);
    $router->get("/origin/{origin}", [UrlController::class, 'getURLByOrigin']);
});

$router->group(['prefix'=>'internal'], function() use ($router) {
    $router->get('/cache/reload',  [UrlController::class, 'reloadCache']);
    $router->get('/db/all',  [UrlController::class, 'getAllUrlsFromDB']);
});

$router->get('/health',  [HealthCheckController::class, 'healthcheck']);
