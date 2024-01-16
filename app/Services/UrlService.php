<?php

namespace App\Services;

use App\Models\Url;
use App\Repository\UrlRepository;
use App\Utility\CharacterGenerator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Log;

class UrlService
{

    public function __construct(private DatabaseManager $database, private UrlRepository $urlRepository)
    {
    }

    public function add(array $data): Url
    {
        $userUrl  = $data['url'];
        $alias = $data['alias'];
        $path = $data['customPath'];

        // check that it exists in DB and then return it
        // else create a new one
        // save it in redis
        $this->database->beginTransaction();
        if (empty($path)) {
            $path = CharacterGenerator::generateRandomString();
        }
        try {
            $url = Url::create(['base_url_id' => 1, 'to' => $userUrl, 'alias' => $alias, 'path' => $path]);
        } catch (\Exception $e) {
            $this->database->rollBack();
            Log::error($e->getMessage());
            throw $e;
        }

        return $url;
    }


    public function getURLByPath(string $path): string
    {
        return $this->urlRepository->getURLByPath($path);
    }
}
