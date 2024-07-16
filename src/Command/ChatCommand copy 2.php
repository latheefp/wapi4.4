<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use App\Chat\ChatServer;
use React\EventLoop\Factory;

use App\Service\ServiceContainer;

class ChatCommand extends Command
{
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        
        // Add options
        $parser->addOption('action', [
            'short' => 'a',
            'help' => 'Specify action: start or send',
            'choices' => ['start', 'send'],
            'default' => 'start'
        ]);
        
        $parser->addOption('record', [
            'short' => 'r',
            'help' => 'DB record ID of Chats DB to process the BData',
            'default' => null
        ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $action = $args->getOption('action');
        $recordId = $args->getOption('record');

        if (intval(getenv('WSENABLED')) == true) {
            $io->out("WSENABLED Enabled, processing");

            if ($action === 'start') {
                $this->start($io);
            } elseif ($action === 'send') {
                $this->send($io, $recordId);
            } else {
                $io->out("Unknown action: $action");
                return Command::CODE_ERROR;
            }
        } else {
            $io->out("WSENABLED is disabled");
            return Command::CODE_ERROR;
        }
    }

    public function start(ConsoleIo $io)
    {
        $io->out('Starting WebSocket server...');
        
        $loop = Factory::create();
        $chatServer = ChatServer::getInstance($loop);

        // Register the ChatServer in the service container
        ServiceContainer::set('ChatServer', $chatServer);

        // Check if it was set correctly
        $retrievedChatServer = ServiceContainer::get('ChatServer');

        debug($retrievedChatServer);
        // if ($retrievedChatServer !== null) {
        //     $io->out('ChatServer instance has been set correctly.');
        // } else {
        //     $io->out('Failed to set ChatServer instance.');
        // }

        $loop->addPeriodicTimer(5, function () use ($chatServer) {
            $chatServer->pollDatabase();
        });

        $io->out('Running WebSocket server...');
        $chatServer->run();

        $io->out('Running Loop with LoopInterface: ' . get_class($loop));
        $loop->run();
    }

    public function send(ConsoleIo $io, $recordId)
    {
        $io->out('Sending data...');

        // Retrieve the ChatServer instance from the service container
        // $chatServer = ServiceContainer::get('ChatServer');
        // $client = $chatServer->getClient();
        // debug($client);
        $chatServer = ChatServer::getInstance(); // Reuse the existing instance
        $client = $chatServer->getClient(); // Access clients if needed

        // if ($chatServer !== null) {
        //     if ($recordId !== null) {
        //         $io->out("Processing record ID: $recordId");
        //         // Logic to process the record ID and send data
        //         // Example:
        //         $result = $chatServer->processAndSendData($recordId);
        //         $io->out("Result: " . print_r($result, true));
        //     } else {
        //         $io->out("No record ID provided. Cannot process data.");
        //     }
        // } else {
        //     $io->out('ChatServer instance is not available.');
        // }
    }
   
}
