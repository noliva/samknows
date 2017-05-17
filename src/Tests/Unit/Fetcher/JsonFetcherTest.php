<?php

namespace App\Tests\Unit\Fetcher;

use App\Fetcher\JsonFetcher;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;

class JsonFetcherTest extends TestCase
{
    public function testCanFetchAndTransformData()
    {
        $validJson = file_get_contents(__DIR__ . '/../../fixtures/valid.json');

        $fetcher = new JsonFetcher();

        $client = $this->prophesize(ClientInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $client->request(Argument::any(), Argument::any())->shouldBeCalled()->willReturn($response);
        $response->getStatusCode()->shouldBeCalled()->willReturn(200);
        $response->getBody()->shouldBeCalled()->willReturn($validJson);

        $data = $fetcher->fetchFromUrl($client->reveal(), 'test.url');

        $expectedResponse = [
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
                            'timestamp' => '2017-02-28 17:00:00',
                            'value'     => 1214720
                        ]
                    ],
                    'latency' => [
                        [
                            'timestamp' => '2017-02-22 16:00:00',
                            'value'     => 44868
                        ]
                    ],
                    'packet_loss' => [
                        [
                            'timestamp' => '2017-02-08 05:00:00',
                            'value'     => 0.12
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedResponse, $data);
    }
}
