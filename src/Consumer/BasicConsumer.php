<?php 

namespace PlayingWithRabbitMq\Consumer;

use PlayingWithRabbitMq\Consumer\Consumer;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class ExampleConsumer
 * @author Carlos Belisario <carlos.belisario.gonzalez@gmail.com>
 */
class BasicConsumer extends Consumer
{
    /**
     * example of consumer queue, put the logic in the process method
     * @param AMQPMessage $message
     */
    public function process(AMQPMessage $message)
    {
        echo "Message " . $message->body . PHP_EOL;                
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }
}

