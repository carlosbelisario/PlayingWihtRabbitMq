<?php

namespace Tests\PlayingWithRabbitMq\Publisher\Transport;


use PlayingWithRabbitMq\Publisher\Transport\RawTransport;

class RawTransportTest extends \PHPUnit_Framework_TestCase
{
    public function testTransportOk()
    {
        $data = "raw data";
        $transport = new RawTransport($data);
        $response = $transport->transport();
        $this->assertInternalType('string', $response);
        $this->assertEquals($data, $response);
    }
}
