<?php
// src/Command/ProcessQueueCommand.php

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use App\Service\RabbitMQService;

class ProcessQueueCommand extends Command

{
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription('Process messages from a RabbitMQ queue');
        return $parser;
    }

    function execute(Arguments $args, ConsoleIo $io): ?int
    {
        // Instantiate RabbitMQService with connection parameters
        $rabbitMQService = new RabbitMQService(
            env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'),
            env('RABBITMQ_DEFAULT_USER'),
            env('RABBITMQ_DEFAULT_PASS'),
            env('RABBITMQ_VHOST')
        );


        // Define a callback function to process messages
        // $callback = function ($message) use ($io) {
        //     // Process message
        //     $io->out('Received message: ' . $message->body);
        //     // Implement your message processing logic here
        // };

        $callback = function ($message, $io = null) {
            // Process message
            if ($io !== null) {
                $io->out('Received message: ' . $message->body);
            }
            // Implement your message processing logic here
        };

        // Call consumeMessage method with the callback function
        $rabbitMQService->consumeMessage('example_queue', $callback);

        return Command::CODE_SUCCESS;
    }
}
