<?php

namespace App\Console;

use App\Generator\MetricGenerator;
use App\Processor\MetricProcessor;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchDataConsole extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('app:import');

        $this->addArgument('url', InputArgument::REQUIRED, 'url');

        $this->addOption('host', null, InputOption::VALUE_OPTIONAL, 'host', '127.0.0.1');
        $this->addOption('dbname', null, InputOption::VALUE_OPTIONAL, 'dbname', 'samknows');
        $this->addOption('user', null, InputOption::VALUE_OPTIONAL, 'user', 'root');
        $this->addOption('password', null, InputOption::VALUE_OPTIONAL, 'password', 'root');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $host = $input->getOption('host');
        $dbName = $input->getOption('dbname');
        $user = $input->getOption('user');
        $password = $input->getOption('password');

        $conn = $this->getConnection($host, $dbName, $user, $password);
        $this->setUp($conn);

        $client = new Client();
        $response = $client->get($url);
        $report = $this->decode($response->getBody());

        $output->writeln('Processing file');
        try {
            $metricProcessor = new MetricProcessor($conn);
            $metricGenerator = new MetricGenerator($report);
            $metricProcessor->process($metricGenerator);
        } catch (\PDOException $e) {
            throw new \Exception(sprintf('Error processing data, reason: %s', $e->getMessage()));
        }
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
     * @param $host
     * @param $dbName
     * @param $user
     * @param $password
     * @return \PDO
     * @throws \Exception
     */
    private function getConnection($host, $dbName, $user, $password) {
        try {
            $conn = new \PDO(sprintf('mysql:host=%s;dbname=%s', $host, $dbName), $user, $password);
        } catch (\PDOException $e) {
            throw new \Exception(sprintf('Error connecting to database, reason: %s', $e->getMessage()));
        }

        return $conn;
    }

    private function setUp(\PDO $conn) {
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
