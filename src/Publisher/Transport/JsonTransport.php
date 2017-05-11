<?php

namespace PlayingWithRabbitMq\Publisher\Transport;

use PlayingWithRabbitMq\Exception\JsonTransportException;

/**
 * Class JsonTransport
 * @package PlayingWihtRabbitMq\Publisher\Transport
 */
class JsonTransport implements TransportInterface
{
    /**
     * @var mixed $data
     */
    private $data;

    /**
     * JsonTransport constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return string 
     * @throws JsonTransportException
     */
    public function move()
    {
        if (!$this->isValid()) {
            throw new JsonTransportException('the data pass should be a resource');
        }

        return $this->toJson($this->data);
    }

    /**
     * @return bool
     */
    private function isValid()
    {
        json_decode($this->data);
        if (json_last_error() === JSON_ERROR_NONE) {
            return true;
        }
        
        return is_resource($this->data);
    }

    /**
     * @param mixed $resource
     * @return string
     */
    private function toJson($resource)
    {
        return json_encode($resource);
    }
}