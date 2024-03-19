<?php
namespace App\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private $connection;

    public function __construct($host, $port, $user, $password, $vhost)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
    }

    public function publishMessage($queueName, $message)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queueName, false, true, false, false);
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, '', $queueName);
        $channel->close();
        $this->connection->close();
    }

    // Add methods for consuming messages, etc.

    public function consumeMessage($queueName, callable $callback)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queueName, false, true, false, false);
    
        $callbackWrapper = function ($msg) use ($callback) {
            // Process the message
            $callback($msg);
        };
    
        $channel->basic_consume($queueName, '', false, true, false, false, $callbackWrapper);
    
        // Keep the channel open until it's explicitly closed
        try {
            while (count($channel->callbacks)) {
                $channel->wait();
            }
        } finally {
            $channel->close();
            $this->connection->close();
        }
    }
    
    
}