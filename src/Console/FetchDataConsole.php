<?php

namespace App\Console;

use App\Generator\MetricGenerator;
use App\Processor\MetricProcessor;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchDataConsole extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('app:import');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setUp();

        $client = new Client();
        $response = $client->get('http://tech-test.sandbox.samknows.com/php-2.0/testdata.json');
        $report = $this->decode($response->getBody());

        $output->writeln('Processing file');
        $metricProcessor = new MetricProcessor($this->getConnection());
        $metricGenerator = new MetricGenerator($report);
        $metricProcessor->process($metricGenerator);
        $output->writeln('Finished processing');
    }

    /**
     * @param $data
     * @return Array | []
     * @throws \Exception
     */
    private function decode($data) {
        $decodedData = json_decode($data, true);

        if ($decodedData === null && JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(sprintf("Error decoding json, reason: %s", json_last_error_msg()));
        }

        return $decodedData;
    }

    /**
     * @return \PDO
     * @throws \Exception
     */
    private function getConnection() {
        try {
            $conn = new \PDO('mysql:host=127.0.0.1;dbname=samknows', 'root', 'root');
        } catch (\PDOException $e) {
            throw new \Exception(sprintf('Error connecting to database, reason: %s', $e->getMessage()));
        }

        return $conn;
    }

    private function setUp() {
        $conn = $this->getConnection();

        $conn->exec(
          'CREATE TABLE IF NOT EXISTS metrics (
                id INT NOT NULL AUTO_INCREMENT,
                unit_id INT NOT NULL,
                metric VARCHAR(255),
                minimum INT NOT NULL,
                maximum INT NOT NULL,
                mean INT NOT NULL,
                median INT NOT NULL,
                sample_size INT NOT NULL,
                `date` TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            );'
        );
    }
}
