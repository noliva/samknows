<?php

namespace App\Generator;

class MetricGenerator
{
    /**
     * @var array
     */
    private $metrics;

    /**
     * @param array $metrics
     */
    public function __construct(array $metrics) {
        $this->metrics = $metrics;
    }

    /**
     * @return \Generator | []
     */
    public function generate()
    {
        foreach ($this->metrics as $unit) {
            $unitId = $unit['unit_id'];
            foreach ($unit['metrics'] as $metric => $measures) {
                foreach ($measures as $data) {
                    yield [
                        'unit_id' => $unitId,
                        'metric' => $metric,
                        'value' => $data['value'],
                        'date' => $data['timestamp'],
                    ];
                }
            }
        }
    }
}
