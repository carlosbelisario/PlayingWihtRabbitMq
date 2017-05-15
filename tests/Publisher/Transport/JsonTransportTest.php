<?php

namespace Tests\PlayingWithRabbitMq\Publisher\Transport;

use PlayingWithRabbitMq\Publisher\Transport\JsonTransport;

class JsonTransportTest extends \PHPUnit_Framework_TestCase
{
    const INVALID_TO_JSON = 'not valid data';

    /**
     * @dataProvider dataProvider
     * @param $data
     */
    public function testTransportOk($data)
    {
        $transport = new JsonTransport($data);
        $response = $transport->transport();
        $this->assertInternalType('string', $response);
        json_decode($response);
        $this->assertTrue(json_last_error() === JSON_ERROR_NONE);
    }

    public function dataProvider()
    {
        $array = ['name' => 'my name is Carlos'];
        $object = (object) ['name' => 'my name is Carlos'];
        $string = '{"name":"my name is Carlos"}';
        return [
            [$array],
            [$object],
            [$string]
        ];
    }

    /**
     * @expectedException \PlayingWithRabbitMq\Exception\JsonTransportException
     */
    public function testTransportNotOk()
    {
        $transport = new JsonTransport(self::INVALID_TO_JSON);
        $response = $transport->transport();
        $this->assertInternalType('string', $response);
        json_decode($response);
        $this->assertTrue(json_last_error() === JSON_ERROR_NONE);
    }
}
