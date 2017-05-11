<?php

namespace PlayingWithRabbitMq\Consumer;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
            echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
            $channel->basic_qos($size, $count, $global);
            $channel->basic_consume($queue, '', false, false, false, false, array($this, "process"));

            while(count($channel->callbacks)) {
                $channel->wait();
            }
            $channel->close();
            $this->rabbitConnection->close();
        } catch (\Exception $e) {
            //manage the exception
            echo $e->getMessage();
        }
    }

    /**
     * @param AMQPMessage $message
     */
    public abstract function process(AMQPMessage $message);
}
