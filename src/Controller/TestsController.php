<?php
// File: src/Controller/TestsController.php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use App\Chat\ChatServer;
use App\Service\ServiceContainer;
use WebSocket\Client;

class TestsController extends AppController
{
    protected $chatServer;

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


    function senddata(){
        try {
            $client = new Client("ws://localhost:8080");
            $data = json_encode(['type' => 'ProcessChatsID', 'recordId' => 10]);
            $client->send($data);
        
            $response = $client->receive();
            echo "Received response: $response\n";
        
        } catch (\Exception $e) {
            echo "Error: {$e->getMessage()}\n";
        }
    }
}
