<?php

namespace PlayingWithRabbitMq;

/**
 * Class QueueAMQ
 * @package PlayingWithRabbitMq
 */
class QueueAMQ
{
    public $name;
    
    public $passive;
    
    public $durable;
    
    public $exclusive;
    
    public $autoDelete;
    
    public $nowait;
    
    public $priority;
    
    public $ticket;

    /**
     * QueueAMQ constructor.
     * @param $name
     * @param $passive
     * @param $durable
     * @param $exclusive
     * @param $autoDelete
     * @param $nowait
     * @param $priority
     * @param $ticket
     */
    public function __construct(
        $name,
        $passive = false, 
        $durable = true, 
        $exclusive = false, 
        $autoDelete = false, 
        $nowait = false, 
        $priority = null, 
        $ticket = null
    ) {
        $this->name = $name;
        $this->passive = $passive;
        $this->durable = $durable;
        $this->exclusive = $exclusive;
        $this->autoDelete = $autoDelete;
        $this->nowait = $nowait;
        $this->priority = $priority;
        $this->ticket = $ticket;
    }
}
