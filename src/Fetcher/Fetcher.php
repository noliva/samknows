<?php

namespace App\Fetcher;

use GuzzleHttp\ClientInterface;

interface Fetcher
{
    /**
     * @param ClientInterface $client
     * @param $url
     *
     * @return Array | []
     *
     * @throws \Exception
     */
    public function fetchFromUrl(ClientInterface $client, $url);
}
