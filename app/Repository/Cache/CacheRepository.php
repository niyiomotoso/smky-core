<?php
namespace App\Repository\Cache;


use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CacheRepository
{
    private const URL_PATH_PREFIX = 'url:path:';

    private mixed $client;
    public function __construct()
    {
        $this->client = app('redis');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUrlByPath($path): string | null
    {
        return app('redis')->get(self::URL_PATH_PREFIX.$path);
    }

    public function getAllUrlKeys()
    {
        return app('redis')->keys(self::URL_PATH_PREFIX.'*');
    }

    public function deleteAllUrlKeys(): void
    {
        $keys = $this->getAllUrlKeys();
        foreach ($keys as $key) {
            $this->client->del($key);
        }
    }

    public function saveURLMap($path, $to): void
    {
        app('redis')->set(self::URL_PATH_PREFIX.$path, $to);
    }
}
