<?php

namespace Tests\PlayingWithRabbitMq\Publisher\Consumer;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Message\AMQPMessage;
use PlayingWithRabbitMq\Consumer\BasicConsumer;
use PlayingWithRabbitMq\QueueAMQ;
use Prophecy\Argument;

/**
 * Class BasicConsumerTest
 * @package Tests\PlayingWithRabbitMq\Publisher\Consumer
 * @author Carlos Belisario <carlos.belisario.gonzalez@gmail.com>
 */
class BasicConsumerTest extends \PHPUnit_Framework_TestCase
{
    const QUEUE_NAME = "queue_test";

    public function testProcess()
    {
        $message = $this->prophesize(AMQPMessage::class);
        $message->body = 'received';

        $channel = $this->prophesize(AMQPChannel::class);
        $channel->basic_ack(Argument::type('string'))->shouldBeCalled();
        $message->delivery_info = ['channel' => $channel, 'delivery_tag' => 'delivery_tag'];

        $rabbitConnection = $this->prophesize(AMQPStreamConnection::class);

        $consumer = new BasicConsumer($rabbitConnection->reveal());
        $consumer->process($message->reveal());
        $this->expectOutputString('Message ' . $message->body . PHP_EOL);
    }

    public function testRun()
    {
        $channel = $this->prophesize(AMQPChannel::class);
        $channel->queue_declare(
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )
            ->shouldBeCalled()
        ;
        $channel->basic_qos(
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldBeCalled();

        $channel->basic_consume(
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldBeCalled();
        $channel->close()->shouldBeCalled();

        $rabbitConnection = $this->prophesize(AMQPStreamConnection::class);
        $rabbitConnection->channel()->willReturn($channel->reveal())->shouldBeCalled();
        $rabbitConnection->close()->shouldBeCalled();

        $consumer = new BasicConsumer($rabbitConnection->reveal());
        $consumer->run(self::QUEUE_NAME);
    }

    /**
     * @expectedException \PlayingWithRabbitMq\Exception\ConsumerException
     */
    public function testPublishNotOk()
    {
        $rabbitConnection = $this->prophesize(AMQPStreamConnection::class);
        $rabbitConnection->channel()->willThrow(new AMQPRuntimeException('test fail'));

        $consumer = new BasicConsumer($rabbitConnection->reveal());
        $consumer->run(self::QUEUE_NAME);
    }
}
