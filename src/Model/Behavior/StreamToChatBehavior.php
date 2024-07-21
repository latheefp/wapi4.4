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
        $chatsTable = TableRegistry::getTableLocator()->get('Chats');


        if (empty($entity->contact_stream_id)) {
            // Handle missing contact_stream_id
            Log::debug("Error: contact_stream_id is empty", 'error');
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
            $this->NotifyLiveChat($chat->id);
          //  sleep(200);
            
        } else {
            Log::debug("Behavior: failed due to missing data during edit ".json_encode($entity->toArray)." has disconnected");
        }

        // if(isset($chat->id)){
        //     sleep (100);
        //     Log::debug("Behavior: Calling live messgae.:$chat->id");
        //     $this->NotifyLiveChat($chat->id);
        // }else{
        //     Log::debug("No Chat ID available");
        // }


     }

    //  function NotifyLiveChat($chatid){
    //     Log::debug("StreamToChatBehavior: detected changes in Chat table: $chatid");
    //         $data['type'] = 'ProcessChatsID';
    //         $data['id'] = $chatid;
    //         $options = [
    //             'timeout' => 30 // Connection and read timeout in seconds
    //         ];
    //         $client = new Client(getenv('CHAT_INTERNAL_URL'), $options);
    //         Log::debug("StreamToChatBehavior: Calling websocket from Listener: ".getenv('CHAT_INTERNAL_URL'));
    //         $client->send(json_encode($data));
    //         $response = $client->receive();
    //         Log::debug("Received response: $response");
    //  }

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
