<?php

namespace App\Services;

use App\Exceptions\BadRequestException;
use App\Models\Url;
use App\Repository\UrlRepository;
use App\Utility\CharacterGenerator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Log;

class UrlService
{

    public function __construct(
        private DatabaseManager $database,
        private UrlRepository $urlRepository)
    {
    }

    public function add(array $data): Url
    {
        $userUrl  = $data['url'];
        $alias = $data['alias'];
        $path = $data['customPath'];

        // check that it exists in DB and then return it
//        $urlDetails = Url::where('to', $userUrl)->get()->first();
//        if (!empty($urlDetails)) {
//            return $urlDetails;
//        }

        if (empty($path)) {
            $path = CharacterGenerator::generateRandomString();
        } else {
            // check if custom path exists and return an error
            $urlDetails = Url::where('path', $path)->get()->first();
            if (!empty($urlDetails)) {
                throw new BadRequestException("path already exists, change to something else");
            }
        }

        $this->database->beginTransaction();
        try {
            $url = Url::create(['base_url_id' => 1, 'to' => $userUrl, 'alias' => $alias, 'path' => $path]);
        } catch (\Exception $e) {
            $this->database->rollBack();
            Log::error($e->getMessage());
            throw $e;
        }

        // save it in redis
        $this->urlRepository->saveURLMapInCache($path, $userUrl);
        $this->database->commit();

        return $url;
    }

    public function getURLByPath(string $path): array | null
    {
        return $this->urlRepository->getURLByPath($path);
    }

    public function getOriginByPathFromCache(string $path): string | null
    {
        return $this->urlRepository->getOriginByPathFromCache($path);
    }

    public function getURLByOrigin(string $origin): string | null
    {
        return $this->urlRepository->getURLByOrigin($origin);
    }


    public function getAllURLs(): array
    {
        return $this->urlRepository->getAllURLs();
    }

    public function reloadCache(): int
    {
        return $this->urlRepository->reloadCache();
    }
}
