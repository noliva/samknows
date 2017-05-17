<?php

namespace App\Fetcher;

use GuzzleHttp\Client;

interface Fetcher
{
    /**
     * @param Client $client
     * @param $url
     *
     * @return Array | []
     *
     * @throws \Exception
     */
    public function fetchFromUrl(Client $client, $url);
}
