<?php

namespace App\Model\Behavior; //Added  $this->addBehavior('StreamToChat'); to streamsTable in intiatlisiztion function.

use Cake\ORM\Behavior;
use Cake\Event\Event;
use ArrayObject;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Log\LogTrait;
class StreamToChatBehavior extends Behavior
{
    public function afterSave(Event $event, $entity, ArrayObject $options)
    {
        $chatsTable = TableRegistry::getTableLocator()->get('Chats');
        //Log::debug("Save failed due to missing data during adding". $entity);
        Log::debug("New Change: ".$entity);
        if ($entity->isNew()) {
            Log::debug("New record: ".$entity);
            if (!empty($entity->contact_stream_id) && !empty($entity->account_id) && (!empty($entity->recievearray) || !empty($entity->sendarray))) {
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
                    //     print "Chats saved";
                } else {
                    Log::debug("Save failed in New $entity");
                }
            }else{
                Log::debug("Save failed due to missing data during adding". $entity);
            }
            


        } else {
            Log::debug("Edit record from Streams: ".$entity);
            if (isset($entity->recievearray) || isset($entity->sendarray)) {
                $chat = $chatsTable->find()
                    ->where(['stream_id' => $entity->id])
                    ->first();

                if ($chat) { //trying to edit existing record
                    if (!empty($entity->contact_stream_id) && !empty($entity->account_id) && (!empty($entity->recievearray) || !empty($entity->sendarray))) {
                        $chat = $chatsTable->patchEntity($chat, [
                            'contact_stream_id' => $entity->contact_stream_id,
                            'sendarray' => $entity->sendarray,
                            'recievearray' => $entity->recievearray,
                            'account_id' => $entity->account_id,
                            'created' => $entity->created,
                        ]);
    
                        if ($chatsTable->save($chat)) {
                            // Successfully updated
                        } else {
                            debug($chat->getErrors());
                        }

                    }else{
                        Log::debug("SAve failed due to missing data during edit ".json_encode($$entity->toArray)." has disconnected");
                    }
                    
                } else { // Adding new record in Chats table.
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
                        Log::debug("Saved new row to Chats $chat");
                    } else {
                        Log::debug("Failed to create new Chats $chat");
                    }
                }
            }else{
                debug($entity);
            }
        }
    }
}

// INSERT INTO chats (contact_stream_id, stream_id, sendarray, recievearray, account_id, created, type)
// SELECT contact_stream_id, id, sendarray, recievearray, account_id, created,type
// FROM streams
// WHERE sendarray IS NOT NULL OR recievearray IS NOT NULL
// ORDER BY created;
