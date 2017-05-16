<?php

namespace App\Processor;

use App\Generator\MetricGenerator;

class MetricProcessor
{
    /**
     * @var \PDO
     */
    private $conn;

    /**
     * @param \PDO $conn
     */
    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param MetricGenerator $metricGenerator
     */
    public function process(MetricGenerator $metricGenerator)
    {
        $this->createTemporaryTable();

        $stmt = $this->conn->prepare(
            'INSERT INTO temporary_metrics (unit_id, metric, `value`, `date`)
             VALUES (:unit_id, :metric, :value, :date);'
        );

        foreach ($metricGenerator->generate() as $metric) {
            $stmt->execute($metric);
        }

        $this->calculate();
    }

    private function createTemporaryTable() {
        $this->conn->exec('
            CREATE TEMPORARY TABLE temporary_metrics (
                id INT NOT NULL AUTO_INCREMENT,
                unit_id INT NOT NULL,
                metric VARCHAR(255),
                `value` INT NOT NULL,
                `date` TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            );
        ');
    }

    private function calculate() {
        $this->conn->exec('
        INSERT INTO metrics (unit_id, metric, minimum, maximum, mean, median, sample_size, `date`)
            SELECT
                unit_id,
                metric,
                MIN(`value`) AS minimum,
                MAX(`value`) AS maximum,
                AVG(value) AS mean,
                0 AS median,
                COUNT(*) AS sample_size,
                `date`
            FROM temporary_metrics
            GROUP BY unit_id, metric, `date`;'
        );
    }
}
