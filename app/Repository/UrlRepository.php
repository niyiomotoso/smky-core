<?php

namespace App\Repository;

use App\Models\Url;
use App\Repository\Cache\CacheRepository;

class UrlRepository
{

    public function getURLByPath(string $path): string | null
    {
        $to = CacheRepository::getUrlByPath($path);
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

    public function saveURLMapInCache($path, $to): bool
    {
        CacheRepository::saveURLMap($path, $to);
        return true;
    }
}
