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
            $to = Url::where('path', $to)->get()->first();
        }

        return $to;
    }
}
