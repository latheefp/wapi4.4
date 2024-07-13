<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use App\Chat\ChatServer;
use React\EventLoop\Factory;


class ChatCommand extends Command
{

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        //example invince -a acount_id -m month -y year
        $parser->addOptions(
             []
        );

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
      
            if (intval(getenv('WSENABLED')) == true) {  //WebSocketEnabled in Env
                print "SNDQRUN Enabled, processing";
                $this->start($io);
            }else{
                $io->out("WSENABLED is disabled");
                return Command::CODE_ERROR;
            }
        

       
    }


    public function initialize(): void
    {
        parent::initialize();
       
    }

    public function start($io)
    {
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
    }

 
    

}
