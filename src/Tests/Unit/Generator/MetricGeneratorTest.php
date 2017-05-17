<?php

namespace App\Tests\Unit\Generator;

use App\Generator\MetricGenerator;
use PHPUnit\Framework\TestCase;

class MetricGeneratorTest extends TestCase
{
    public function testGenerateFromArray()
    {
        $arrayExample = [
            [
                'unit_id' => 1,
                'metrics' => [
                    'download' => [
                        [
                            'timestamp' => '2017-02-10 17:00:00',
                            'value'     => 4670170
                        ]
                    ],
                    'upload' => [
                        [
                            'timestamp' => '2017-02-10 17:00:00',
                            'value'     => 4670170
                        ]
                    ],
                    'latency' => [
                        [
                            'timestamp' => '2017-02-10 17:00:00',
                            'value'     => 4670170
                        ]
                    ],
                    'packet_loss' => [
                        [
                            'timestamp' => '2017-02-10 17:00:00',
                            'value'     => 46701.70
                        ]
                    ]
                ]
            ]
        ];

        $results = [
            [
                'unit_id' => 1,
                'metric' => 'download',
                'value' => 4670170,
                'date' => '2017-02-10 17:00:00'
            ],
            [
                'unit_id' => 1,
                'metric' => 'upload',
                'value' => 4670170,
                'date' => '2017-02-10 17:00:00'
            ],
            [
                'unit_id' => 1,
                'metric' => 'latency',
                'value' => 4670170,
                'date' => '2017-02-10 17:00:00'
            ],
            [
                'unit_id' => 1,
                'metric' => 'packet_loss',
                'value' => 4670170,
                'date' => '2017-02-10 17:00:00'
            ],
        ];

        $generator = new MetricGenerator($arrayExample);

        foreach ($generator->generate() as $key => $metric) {
            $this->assertEquals($results[$key], $metric);
        }
    }
}
