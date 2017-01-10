<?php 

namespace PlayingWihtRabbitMq\Consumer;

use PlayingWihtRabbitMq\Consumer\Consumer;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class ExampleConsumer
 * @author Carlos Belisario <carlos.belisario.gonzalez@gmail.com>
 */
class ExampleConsumer extends Consumer
{
    /**
     * example of consumer queue, put the logic in the process method
     * @param AMQPMessage $message
     */
    public function process(AMQPMessage $message)
    {
        echo " [x] Received ", $message->body, "\n";
        sleep(10);
        echo " [x] Done", "\n";
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }
}

