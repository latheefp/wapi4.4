<?php
//src/Event/ChatEventListener.php
namespace App\Event;
use React\EventLoop\Factory;

use Cake\Event\EventListenerInterface;
use App\Chat\ChatServer;
use Cake\Log\LogTrait;
class ChatEventListener implements EventListenerInterface
{
    use LogTrait; // Include the LogTrait
    public function implementedEvents(): array
    {
        return [
            'Model.afterSave' => 'onAfterSave' //expect no edit happends to chats tables, only creatoin. 
        ];
    }

    public function onAfterSave($event, $entity, $options)
    {
        // Check if it's the specific table you are interested in
        if ($event->getSubject()->getTable() === 'chats') {
            $this->log('The table is : ' . $event->getSubject()->getTable(), 'debug');
            // Get the loop instance from your ChatServer singleton
            $loop = Factory::create();
            $chatServer = ChatServer::getInstance($loop);
            // Call a method on your ChatServer
           $result=$chatServer->notifyClients($entity);
           $this->log('The result is : ' . $result, 'debug');
       //     debug($failed);
        }else{
          //  $this->log('The table is : ' . $event->getSubject()->getTable(), 'debug');
        }
    }
}
