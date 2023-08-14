<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumeUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:consume-user-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run this command to consume results from RabbitMQ user queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'rabbitmq'),
            env('RABBITMQ_PORT', '5672'),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );
        $channel = $connection->channel();
        $channel->queue_declare(
            env('RABBITMQ_QUEUE', 'users'),
            false,
            false,
            false,
            false
        );

        echo "[...] Чакам, чакам вече час... \n Спри ме: CTRL+C\n";

        $callback = function ($msg) {
            echo '[✓] Получено:  ', $msg->body, "\n";
            Log::info('Резултат от Q: ' . $msg->body);
            echo '[✓] Записано в логa:  ', $msg->body, "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);

        $channel->basic_consume(
            env('RABBITMQ_QUEUE', 'users'),
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();

        return Command::SUCCESS;
    }
}
