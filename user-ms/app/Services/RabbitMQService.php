<?php

namespace App\Services;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'rabbitmq'),
            env('RABBITMQ_PORT', '5672'),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare(
            env('RABBITMQ_QUEUE', 'users'),
            false,
            false,
            false,
            false
        );
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function send(string $message): void
    {
        $this->channel->basic_publish(
            new AMQPMessage($message),
            '',
            env('RABBITMQ_QUEUE', 'users')
        );
    }
}
