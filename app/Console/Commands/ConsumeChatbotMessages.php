<?php

namespace App\Console\Commands;

use App\Jobs\ProcessScheduleJob;
use App\Models\Message;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Illuminate\Support\Facades\Log;

class ConsumeChatbotMessages extends Command
{
    protected $signature = 'rabbitmq:consume-chatbot';
    protected $description = 'Consume chatbot messages from RabbitMQ';

    public function handle()
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'localhost'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );
        $channel = $connection->channel();

        $channel->queue_declare('chatbot-messages', false, true, false, false);

        $callback = function ($msg) {
            $data = json_decode($msg->body, true);
            // Process $data as needed, e.g., store in DB, trigger events, etc.
            Log::info('Received chatbot message', $data);


            if (!$data) {
                Log::error('Invalid JSON received', ['body' => $msg->body]);
                $msg->ack();
                return;
            } elseif (isset($data['content'], $data['user_id'])) {
                // Store to Message model
                $message = Message::create([
                    'user_id' => $data['user_id'],
                    'content' => $data['content'],
                ]);

                Log::info('Message saved to database', ['message_id' => $message->id]);
            } elseif (isset($data['food_id'], $data['user_id'], $data['schedule_at'])) {
                ProcessScheduleJob::dispatch($data)->queue('create-schedule');

                Log::info('Schedule job dispatched', ['schedule_data' => $data]);

            } else {
                Log::error('Invalid JSON received', ['body' => $msg->body]);
            }
            $msg->ack();
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('chatbot-messages', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
