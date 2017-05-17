<?php

namespace App\Service;

use App\Database\Mysql;
use App\Fetcher\Fetcher;
use App\Generator\MetricGenerator;
use App\Processor\MetricProcessor;
use GuzzleHttp\Client;

class ProcessReportService
{
    /**
     * @var Mysql
     */
    private $db;

    /**
     * @param string $host
     * @param string $dbName
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $dbName, string $username, string $password)
    {
        $this->db = new Mysql($host, $dbName, $username, $password);
        $this->db->setUp();
    }

    /**
     * @param Fetcher $fetcher
     * @param string $url
     *
     * @throws \Exception
     */
    public function run(Fetcher $fetcher, string $url)
    {
        try {
            $client = new Client();
            $metricProcessor = new MetricProcessor($this->db->getConnection());
            $metricGenerator = new MetricGenerator($fetcher->fetchFromUrl($client, $url));
            $metricProcessor->process($metricGenerator);
        } catch (\PDOException $e) {
            throw new \Exception(sprintf('Error processing data, reason: %s', $e->getMessage()));
        }
    }
}
