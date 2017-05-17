<?php

namespace App\Console;

use App\Fetcher\JsonFetcher;
use App\Service\ProcessReportService;
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

        $this->addOption('host', 'ho', InputOption::VALUE_REQUIRED, 'host');
        $this->addOption('dbname', 'db', InputOption::VALUE_REQUIRED, 'dbname');
        $this->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'username');
        $this->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'password');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        $host = $input->getOption('host');
        $dbName = $input->getOption('dbname');
        $username = $input->getOption('username');
        $password = $input->getOption('password');

        $options = ['host', 'dbName', 'username', 'password'];
        foreach ($options as $option) {
            if (is_null(${$option}) || empty(${$option})) {
                throw new \Exception(sprintf('Option %s can not be null', $option));
            }
        }

        $processReport = new ProcessReportService($host, $dbName, $username, $password);
        $fetcher = new JsonFetcher();

        $output->writeln('Processing data');
        $processReport->run($fetcher, $url);
        $output->writeln('Finished processing data');
    }
}
