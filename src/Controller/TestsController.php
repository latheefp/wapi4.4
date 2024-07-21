<?php

namespace App\Controller;

use App\Controller\AppController;
// use Cake\Event\EventInterface;
// use App\Chat\ChatServer;
// use App\Service\ServiceContainer;
use WebSocket\Client;
// use Cake\Core\Configure;
// use Cake\Log\Log;
// use Queue\Model\Table\QueuedJobsTable;

use Queue\Job\Job;
// use Cake\ORM\TableRegistry;
use Queue\Shell\Task\QueueTask;


// use App\Controller\AppController;
// use Cake\ORM\TableRegistry;
use Cake\Log\Log;




class TestsController extends AppController
{
   // protected $chatServer;


    



   public function testq()
   {
       // Get the QueuedJobs table instance
       $queuedJobsTable = TableRegistry::getTableLocator()->get('Queue.QueuedJobs');
       
       // Job data
       $jobData = ['id' => 100];
       
       // Create job in the queue
       $result = $queuedJobsTable->createJob('ProcessChat', $jobData);
       
       // Log the result for debugging
       Log::debug('Job creation result: ' . json_encode($result));
   }

  //  public function initialize(): void
    // {
    //     parent::initialize();
    //     // Retrieve the ChatServer instance from the service container
    //     $this->chatServer = ServiceContainer::get('ChatServer');
    // }

    // public function chatserver()
    // {
    //     $this->viewBuilder()->setLayout('ajax');

    //     if ($this->chatServer !== null) {
    //         debug($this->chatServer->getClients());
    //     } else {
    //         $this->log("ChatServer instance is null.", 'error');
    //     }
    // }


    function senddata($id){
        try {
            $client = new Client("ws://localhost:8080");
            $data = json_encode(['type' => 'ProcessChatsID', 'id' => $id]);
            $client->send($data);
        
            $response = $client->receive();
            echo "Received response: $response\n";
        
        } catch (\Exception $e) {
            echo "Error: {$e->getMessage()}\n";
        }
    }

    function testws($id){
        $entityArray['id'] = $id;
        $entityArray['contact_stream_id'] = 1;
        $entityArray['account_id'] = 1;
        $entityArray['stream_id'] = 251566;
        $entityArray['type'] = 'ProcessChatsID';


        $data = json_encode($entityArray);
        $options = [
            'timeout' => 30 // Connection and read timeout in seconds
        ];
        $client = new Client(getenv('CHAT_INTERNAL_URL'), $options);
        $this->log("Calling websocket from Listener: ".getenv('CHAT_INTERNAL_URL') . " with data",'debug');
        $client->send($data);
        $response = $client->receive();
        $this->log("Received response: $response",'debug');
    }


    function testcommand()
    {
       // $this->log('ChatEventListener: detected changes in Chat table: ' . $event->getSubject()->getTable(), 'debug');
        $cmd = ROOT . "/bin/cake chat -a newchat -r " . 264144;
        $this->log("Testcontroller: Running $cmd", 'debug');
        exec("$cmd > /dev/null 2>&1 &");
        $this->log('Testcontroller: Command execution triggered.', 'debug');

        $logFile = ROOT . '/logs/command_output.log';
        exec("$cmd > $logFile 2>&1 &");
    }

}
