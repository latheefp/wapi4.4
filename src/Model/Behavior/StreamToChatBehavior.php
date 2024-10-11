<?php

namespace App\Model\Behavior; //Added  $this->addBehavior('StreamToChat'); to streamsTable in intiatlisiztion function.

use Cake\ORM\Behavior;
use Cake\Event\Event;
use ArrayObject;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Log\LogTrait;


use React\EventLoop\Factory;

use Cake\Event\EventListenerInterface;
use App\Chat\ChatServer;

use WebSocket\Client;


use Cake\ORM\EntityInterface;
use Cake\Event\EventInterface;

use Queue\Model\Table\QueuedJobsTable;
class StreamToChatBehavior extends Behavior
{
    public function afterSave(Event $event, $entity, ArrayObject $options)
    {
        if ($entity->live_chat_notified) {
            Log::debug("New entity:live_chat_notified is true: ".$entity->live_chat_notified);
            return;
        }

        
     //   debug($entity);

        if (empty($entity->contact_stream_id)) {
            // Handle missing contact_stream_id
            Log::debug("Error: contact_stream_id is empty".$entity);
            return "Error: contact_stream_id is empty";
        }
        
        if (empty($entity->account_id)) {
            // Handle missing account_id
            Log::debug("Error: account_id is empty", 'error');
            return "Error: account_id is empty";
        }
        
        if (empty($entity->recievearray) && empty($entity->sendarray)) {
            // Handle missing receivearray and sendarray
            Log::debug("Error: both recievearray and sendarray are empty", 'error');
            return "Error: both recievearray and sendarray are empty";
        }
        
        // If all checks pass, proceed with your logic
       
        $chatsTable = TableRegistry::getTableLocator()->get('Chats');
        
        $chat = $chatsTable->newEntity([
            'contact_stream_id' => $entity->contact_stream_id,
            'stream_id' => $entity->id,
            'sendarray' => $entity->sendarray,
            'recievearray' => $entity->recievearray,
            'account_id' => $entity->account_id,
            'created' => $entity->created,
            'type' => $entity->type
        ]);

        if ($chatsTable->save($chat)) {
            Log::debug("Behavior: Data saved as chat ID:$chat->id");
            $entity->live_chat_notified = true;
            $StreamsTable = TableRegistry::getTableLocator()->get('Streams');
            $StreamsTable->save($entity);
            sleep (10);
            $this->NotifyLiveChat($chat->id);

            
        } else {
            Log::debug("Behavior: failed due to missing data during edit ".json_encode($entity->toArray)." has disconnected");
        }


     }


     function NotifyLiveChat($chatid){

               // $this->log('ChatEventListener: detected changes in Chat table: ' . $event->getSubject()->getTable(), 'debug');
               $cmd = ROOT . "/bin/cake chat -a newchat -r " . $chatid;
               Log::debug("StreamToChatBehavior: Running $cmd");
               exec("$cmd > /dev/null 2>&1 &");
               Log::debug('StreamToChatBehavior: Command execution triggered.');
       
               $logFile = ROOT . '/logs/command_output.log';
               exec("$cmd > $logFile 2>&1 &");
     }


}

// INSERT INTO chats (contact_stream_id, stream_id, sendarray, recievearray, account_id, created, type)
// SELECT contact_stream_id, id, sendarray, recievearray, account_id, created,type
// FROM streams
// WHERE sendarray IS NOT NULL OR recievearray IS NOT NULL
// ORDER BY created;
