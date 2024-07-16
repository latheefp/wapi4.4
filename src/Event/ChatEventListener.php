<?php
//src/Event/ChatEventListener.php
namespace App\Event;
use React\EventLoop\Factory;

use Cake\Event\EventListenerInterface;
use App\Chat\ChatServer;
use Cake\Log\LogTrait;
use WebSocket\Client;

class ChatEventListener implements EventListenerInterface
{
    use LogTrait; // Include the LogTrait
    public function implementedEvents(): array
    {
        return [
            'Model.afterSave' => 'onAfterSave' //expect no edit happends to chats tables, only creation. 
        ];
    }


    public function onAfterSave($event, $entity, $options)
    {
        // Check if it's the specific table you are interested in
        if ($event->getSubject()->getTable() === 'chats') {
            $this->log('The table is : ' . $event->getSubject()->getTable(), 'debug');
            $entityArray = $entity->toArray();
            $entityArray['type'] = 'ProcessChatsID';
            $data = json_encode($entityArray);
            $client = new Client(getenv('CHAT_INTERNAL_URL'));
            $this->log("Calling websocket: ".getenv('CHAT_INTERNAL_URL'),'debug');
            $client->send($data);
            $response = $client->receive();
            $this->log("Received response: $response",'debug');
    
        }else{
          //  $this->log('The table is : ' . $event->getSubject()->getTable(), 'debug');
        }
    }
}
