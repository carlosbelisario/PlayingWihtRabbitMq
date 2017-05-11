<?php

namespace PlayingWithRabbitMq\Publisher\Transport;

/**
 * Class RawTransport
 * @package PlayingWihtRabbitMq\Publisher\Transport
 */
class RawTransport implements TransportInterface
{
    private $raw;

    /**
     * RawTransport constructor.
     * @param $raw
     */
    public function __construct($raw)
    {
        $this->raw = $raw;
    }

    public function move()
    {
        return $this->raw;
    }
}
