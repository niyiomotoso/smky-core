<?php
namespace App\Repository\Cache;


use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CacheRepository
{
    private const URL_PATH_PREFIX = 'url:path:';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getUrlByPath($path)
    {
        return app('redis')->get(self::URL_PATH_PREFIX.$path);
    }

    public static function saveURLMap($path, $to)
    {
        app('redis')->set(self::URL_PATH_PREFIX.$path, $to);
    }
}
