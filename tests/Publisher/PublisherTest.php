<?php

namespace Tests\PlayingWithRabbitMq\Publisher;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Message\AMQPMessage;
use PlayingWithRabbitMq\MessageFactory;
use PlayingWithRabbitMq\Publisher\Publisher;
use PlayingWithRabbitMq\Publisher\Transport\TransportInterface;
use PlayingWithRabbitMq\QueueAMQ;
use Prophecy\Argument;

class PublisherTest extends \PHPUnit_Framework_TestCase
{
    const MESSAGE = "some message";

    public function testInstance()
    {
        $rabbitConnection = $this->prophesize(AMQPStreamConnection::class)->reveal();
        $messageFactory = $this->prophesize(MessageFactory::class)->reveal();
        $publisher = new Publisher($rabbitConnection, $messageFactory);
        $this->assertInstanceOf(Publisher::class, $publisher);
    }

    public function testPublishOk()
    {
        $channel = $this->prophesize(AMQPChannel::class);
        $channel->queue_declare(
            Argument::type('string'),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )
            ->shouldBeCalled()
        ;
        $channel->basic_publish(
            Argument::type(AMQPMessage::class),
            Argument::type('string'),
            Argument::type('string')
        )
            ->shouldBeCalled()
        ;
        $channel->close()->shouldBeCalled();

        $rabbitConnection = $this->prophesize(AMQPStreamConnection::class);
        $rabbitConnection->channel()->willReturn($channel->reveal())->shouldBeCalled();
        $rabbitConnection->close()->shouldBeCalled();

        $message = $this->prophesize(AMQPMessage::class)->reveal();
        $messageFactory = $this->prophesize(MessageFactory::class);
        $messageFactory->create(
            Argument::type(TransportInterface::class),
            Argument::type('integer'),
            Argument::type('integer')
        )
            ->willReturn($message)
            ->shouldBeCalled()
        ;
        $queue = new QueueAMQ('test');
        $transport = $this->prophesize(TransportInterface::class);
        $transport->transport()->willReturn(self::MESSAGE);
        $publisher = new Publisher($rabbitConnection->reveal(), $messageFactory->reveal());
        $this->assertTrue($publisher->publish($queue, $transport->reveal()));
    }

    /**
     * @expectedException \PlayingWithRabbitMq\Exception\PublisherException
     */
    public function testPublishNotOk()
    {

        $rabbitConnection = $this->prophesize(AMQPStreamConnection::class);
        $rabbitConnection->channel()->willThrow(new AMQPRuntimeException('test fail'));

        $messageFactory = $this->prophesize(MessageFactory::class);
        $queue = new QueueAMQ('test');
        $transport = $this->prophesize(TransportInterface::class);
        $transport->transport()->willReturn(self::MESSAGE);
        $publisher = new Publisher($rabbitConnection->reveal(), $messageFactory->reveal());
        $this->assertTrue($publisher->publish($queue, $transport->reveal()));
    }
}
