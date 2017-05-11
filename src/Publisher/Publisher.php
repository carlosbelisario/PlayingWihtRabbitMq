<?php

namespace PlayingWithRabbitMq\Publisher;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PlayingWithRabbitMq\Exception\PublisherException;
use PlayingWithRabbitMq\MessageFactory;
use PlayingWithRabbitMq\Publisher\Transport\TransportInterface;
use PlayingWithRabbitMq\QueueAMQ;

/**
 * Class Publisher
 * @package PlayingWihtRabbitMq\Publisher
 */
class Publisher
{
    /**
     * @var AMQPStreamConnection $rabbitConnection
     */
    private $rabbitConnection;

    /**
     * @var MessageFactory $messageFactory
     */
    private $messageFactory;

    /**
     * Publisher constructor.
     * @param AMQPStreamConnection $rabbitConnection
     * @param MessageFactory $messageFactory
     */
    public function __construct(AMQPStreamConnection $rabbitConnection, MessageFactory $messageFactory)
    {
        $this->rabbitConnection = $rabbitConnection;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param QueueAMQ $queue
     * @param TransportInterface $transport
     * @param int $messagePriority
     * @param int $deliveryMode
     * @return bool
     */
    public function publish(
        QueueAMQ $queue,
        TransportInterface $transport,
        $messagePriority = 0,
        $deliveryMode = AMQPMessage::DELIVERY_MODE_PERSISTENT
    ) {
        try {
            $channel = $this->rabbitConnection->channel();
            $channel->queue_declare(
                $queue->name,
                $queue->passive,
                $queue->durable,
                $queue->exclusive,
                $queue->autoDelete,
                $queue->nowait,
                $queue->priority,
                $queue->ticket
            );
            $message = $this->messageFactory->create($transport, $deliveryMode, $messagePriority);
            $channel->basic_publish($message, '', $queue->name);
            $channel->close();
            $this->rabbitConnection->close();
            
            return true;
        } catch (\Exception $e) {
            throw new PublisherException($e->getMessage(), $e->getCode());
        }
    }
}
