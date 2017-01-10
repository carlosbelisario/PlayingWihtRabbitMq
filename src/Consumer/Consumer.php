<?php

namespace PlayingWihtRabbitMq\Consumer;

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
     * Worker constructor.
     * @param AMQPStreamConnection $connection
     */
    public function __construct(AMQPStreamConnection $rabbitConnection)
    {
        $this->rabbitConnection = $rabbitConnection;
    }

    /**
     * @param string $queue
     */
    public function run($queue)
    {
        try {
            $channel = $this->rabbitConnection->channel();
            $channel->queue_declare('payway_procesar', false, true, false, false);
            echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
            $channel->basic_qos(null, 1, null);
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
