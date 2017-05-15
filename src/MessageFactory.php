<?php
/**
 * Created by PhpStorm.
 * User: carlosbelisario
 * Date: 11/05/17
 * Time: 18:01
 */

namespace PlayingWithRabbitMq;


use PhpAmqpLib\Message\AMQPMessage;
use PlayingWithRabbitMq\Publisher\Transport\TransportInterface;

class MessageFactory
{
    public function create(TransportInterface $transport, $deliveryMode, $messagePriority)
    {
        return new AMQPMessage($transport->transport(), $this->getMessageProperty($deliveryMode, $messagePriority));
    }

    /**
     * @param $deliveryMode
     * @param $messagePriority
     * @return array
     */
    public function getMessageProperty($deliveryMode, $messagePriority)
    {
        return [
            'delivery_mode' => $deliveryMode,
            'priority' => $messagePriority
        ];
    }
}