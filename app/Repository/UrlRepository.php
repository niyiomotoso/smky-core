<?php

namespace App\Repository;

use App\Models\Url;
use App\Repository\Cache\CacheRepository;

class UrlRepository
{

    public function getURLByPath(string $path): string
    {
        $to = CacheRepository::getUrlByPath($path);
        if (empty($to)) {
            $urlDetails = Url::where('path', $to)->get(['to'])->first();
            if (!empty($urlDetails)) {
                $to = $urlDetails['to'];
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
