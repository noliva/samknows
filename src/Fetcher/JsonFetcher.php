<?php

namespace App\Fetcher;

use GuzzleHttp\ClientInterface;

class JsonFetcher implements Fetcher
{
    /**
     * @inheritdoc
     */
    public function fetchFromUrl(ClientInterface $client, $url)
    {
        $response = $client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Error fetching data');
        }

        $decodedData = json_decode($response->getBody(), true);

        if ($decodedData === null && JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(sprintf("Error decoding json, reason: %s", json_last_error_msg()));
        }

        return $decodedData;
    }
}
