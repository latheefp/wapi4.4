<?php

namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Event\Event;
use ArrayObject;
use Cake\ORM\TableRegistry;

class StreamToChatBehavior extends Behavior
{
    public function afterSave(Event $event, $entity, ArrayObject $options)
    {
        $chatsTable = TableRegistry::getTableLocator()->get('Chats');
        if ($entity->isNew()) {
            $chat = $chatsTable->newEntity([
                'contact_stream_id ' => $entity->contact_stream_id,
                'stream_id' => $entity->id,
                'sendarray' => $entity->sendarray,
                'recievearray' => $entity->recievearray,
                'account_id' => $entity->account_id,
                'created' => $entity->created,
            ]);

            if ($chatsTable->save($chat)) {
                //     print "Chats saved";
            } else {
                //    debug($chat->getErrors());
            }
            //   debug($entity);

        } else {
            // Update existing record
            if (isset($entity->recievearray) || isset($entity->sendarray)) {
                $chat = $chatsTable->find()
                    ->where(['stream_id' => $entity->id])
                    ->first();

                if ($chat) {

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
                } else {
                    // Handle case where the corresponding chat record is not found
                   // debug('Chat record not found for stream_id: ' . $entity->id);
                   // debug($entity);
                }
            }
        }
    }
}

// INSERT INTO chats (contact_stream_id, stream_id, sendarray, recievearray, account_id, created)
// SELECT contact_stream_id, id, sendarray, recievearray, account_id, created
// FROM streams
// WHERE sendarray IS NOT NULL OR recievearray IS NOT NULL
// ORDER BY created;
