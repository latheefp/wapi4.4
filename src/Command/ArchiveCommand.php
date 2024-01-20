<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\FrozenTime; // Import FrozenTime
use App\Controller\AppController; //(path to your controller).


class ArchiveCommand extends Command
{

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        $parser->addOptions(
            [
                'function' => [
                    'short' => 'f',
                    'help' => 'The function name',
                    'required' => true,
                ],
                'contact_csv' => [
                    'short' => 'o',
                    'help' => 'Options',
                    'required' => false,
                ]
            ]
        );

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        print("running Function => " . $args->getOption('function'). "\n");
        $function = $args->getOption('function');
        
        $this->app->writelog("Running Archive function ".$function);
        $this->$function();
    }


    public function initialize(): void
    {
        parent::initialize();
        $this->app = new AppController();
    }

    

    function cleanq(){
        $this->app = new AppController();
        //cleanSendQ
       
        $retentions=(int)$this->app->_getsettings('q_retention');
        debug("Deleting Send Q older than $retentions");
        $sendTable=$this->getTableLocator()->get('SendQueues');
        $retentionsHoursAgo = FrozenTime::now()->subHours($retentions);
        $query = $sendTable->query();
        $query->delete()
        ->where([
            'http_response_code' => 200,
            'created <' => $retentionsHoursAgo
        ])
            ->execute();


        debug("Deleting Send Q older than $retentions");
        $rcvTable=$this->getTableLocator()->get('RcvQueues');
        $retentionsHoursAgo = FrozenTime::now()->subHours($retentions);
        $query = $rcvTable->query();
        $query->delete()
        ->where([
            'http_response_code' => 200,
            'created <' => $retentionsHoursAgo
        ])
            ->execute();

        
     }
 


}
