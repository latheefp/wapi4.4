<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use App\Chat\ChatServer;
use React\EventLoop\Factory;
use WebSocket\Client; //used on send function
use Cake\ORM\TableRegistry;

//use App\Service\ServiceContainer;

//use josegonzalez\Dotenv\Loader as Dotenv;

class ChatCommand extends Command
{
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        
        // Add options
        $parser->addOption('action', [
            'short' => 'a',
            'help' => 'Specify action: start or send',
            'choices' => ['start', 'newchat'],
            'default' => 'start'
        ]);
        
        $parser->addOption('record', [
            'short' => 'r', //newchat id
            'help' => 'DB record ID of Chats DB to process the BData',
            'default' => null
        ]);

        // $parser->addOption('link', [
        //     'short' => 'i', //newchat id
        //     'help' => 'Remote webhook endpoint to send the chat',
        //     'default' => null
        // ]);


        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $action = $args->getOption('action');
        $recordId = $args->getOption('record');
     //   $webhookurl = $args->getOption('link');

        

            if ($action === 'start') {
                $this->start($io);
            } elseif ($action === 'newchat') {
                $io->out("Running newchat");
                $this->log("ChatCommand: Running newchat. $recordId", 'debug');
                $this->newchat($io, $recordId);
                
            } else {
                $io->out("Unknown action: $action");
                return Command::CODE_ERROR;
            }
       
    }

    public function start(ConsoleIo $io)
    {

        if (intval(getenv('WSENABLED')) == true) {
            $io->out("WSENABLED Enabled, processing");
            $io->out('Starting WebSocket server...');

            $loop = Factory::create();
            $chatServer = ChatServer::getInstance($loop);
    
    
    
            $loop->addPeriodicTimer(5, function () use ($chatServer) {
                $chatServer->pollDatabase();
            });
    
            $io->out('Running WebSocket server...');
            $chatServer->run();
    
            $io->out('Running Loop with LoopInterface: ' . get_class($loop));
            $loop->run();

        } else {
            $io->out("WSENABLED is disabled");
            return Command::CODE_ERROR;
        }
        
    }


   


    public function newchat(ConsoleIo $io, ?string $recordId)
    {
        if (empty($recordId)) {
            $io->out('Record ID is required.');
            return Command::CODE_ERROR;
        }

      //  $io->out('Sending data...');

        //if ($this->updateSendinfo($recordId)) {

            $webhookurl = getenv('CHAT_INTERNAL_URL');
            $options = ['timeout' => 30];
            $client = new Client($webhookurl, $options);
            $data['type'] = 'ProcessChatsID';
            $data['id'] = $recordId;
            $data = json_encode($data);
            try {
              //  $io->out("Sending data to WebSocket server: " . $webhookurl);
                $this->log("ChatCommand: Sending webhook." .   $webhookurl, 'debug');
                $client->send($data);

                // Receive response (optional)
                $response = $client->receive();
                $io->out("Received response: $response");
                $this->log("ChatCommand: Success Response." .  $response, 'debug');
            } catch (Exception $e) {
                $io->out('Error sending data to WebSocket server: ' . $e->getMessage());
                $this->log("ChatCommand: Failed Response." .  Command::CODE_ERROR, 'debug');
                return Command::CODE_ERROR;
            }

            return Command::CODE_SUCCESS;
        // } else {
        //     $this->log("ChatCommand:$recordId already Sent",'debug');
        // }
 
        
    }


    function updateSendinfo($id)
    {
        $chatsTable = TableRegistry::getTableLocator()->get('Chats');
    
        // Fetch the first record that matches the criteria
        $chat = $chatsTable->find()
            ->where(['id' => $id, 'notify' => false])
            ->first();
    
        if ($chat) {
            $this->log("ChatCommand: $id not yet processed",'debug');
            
            // Update the notify field to true
            $chat->notify = true;
    
            // Save the updated entity
            if ($chatsTable->save($chat)) {
                $this->log("ChatCommand: Notification updated for $id",'debug');
                return true;
            } else {
                $this->log("ChatCommand: Update notification failed on $id",'debug');
                return false;
            }
        } else {
            $this->log("ChatCommand: $id already processed",'debug');
            return false;
        }
    }
   
}
