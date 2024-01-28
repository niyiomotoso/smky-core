<?php

namespace App\Repository;

use App\Models\Url;
use App\Repository\Cache\CacheRepository;

class UrlRepository
{

    public function __construct(private CacheRepository $cacheRepository)
    {
    }

    public function getOriginByPathFromCache(string $path): string | null
    {
        $to = $this->cacheRepository->getUrlByPath($path);
        if (empty($to)) {
            $urlDetails = Url::where('path', $to)->get(['to'])->first();
            if (!empty($urlDetails)) {
                $to = $urlDetails['to'];
                // now save the found entry in cache
                $this->saveURLMapInCache($path, $to);
            }
        }

        return $to;
    }

    public function getURLByPath(string $path): array | null
    {
        $data = null;
        $urlDetails = Url::where('path', $path)->get()->first();
        if (!empty($urlDetails)) {
            $to = $urlDetails['to'];
            // now save the found entry in cache
            $this->saveURLMapInCache($path, $to);

            $data = $urlDetails;
        }

        return $data;
    }

    public function getURLByOrigin(string $origin): string | null
    {
        $data = null;
        $urlDetails = Url::where('to', $origin)->get()->first();
        if (!empty($urlDetails)) {
            $data = $urlDetails;
        }

        return $data;
    }

    public function getAllURLs(): array
    {
        return Url::get()->all();
    }

    public function reloadCache(): int
    {
        $urls = $this->getAllURLs();
        $this->cacheRepository->deleteAllUrlKeys();
        foreach ($urls as $url) {
        $this->cacheRepository->saveURLMap($url->path, $url->to);
        }

        return count($urls);
    }

    public function saveURLMapInCache($path, $to): bool
    {
        $this->cacheRepository->saveURLMap($path, $to);
        return true;
    }
}
