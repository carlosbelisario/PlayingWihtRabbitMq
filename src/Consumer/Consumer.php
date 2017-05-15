<?php

namespace PlayingWithRabbitMq\Consumer;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PlayingWithRabbitMq\Exception\ConsumerException;

/**
 * Class Consumer
 * @author Carlos Belisario <carlos.belisario.gonzalez@gmail.com>
 */
abstract class Consumer
{
    /**
     * @var AMQPStreamConnection $rabbitConnection
     */
    private $rabbitConnection;

    /**
     * Consumer constructor
     * @param AMQPStreamConnection $rabbitConnection
     */
    public function __construct(AMQPStreamConnection $rabbitConnection)
    {
        $this->rabbitConnection = $rabbitConnection;
    }

    /**
     * @param string $queue
     * @param string $size
     * @param int $count
     * @param bool global 
     */
    public function run($queue, $size = null, $count = 1, $global = null)
    {
        try {
            $channel = $this->rabbitConnection->channel();
            $channel->queue_declare($queue, false, true, false, false);            
            $channel->basic_qos($size, $count, $global);
            $channel->basic_consume($queue, '', false, false, false, false, array($this, "process"));

            while(count($channel->callbacks)) {
                $channel->wait();
            }
            $channel->close();
            $this->rabbitConnection->close();
        } catch (\Exception $e) {            
            throw new ConsumerException($e->getMessage(), $e->getCode());            
        }
    }

    /**
     * @param AMQPMessage $message
     */
    public abstract function process(AMQPMessage $message);
}
