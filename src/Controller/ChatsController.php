<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
// use Cake\Http\Client;
// use App\Chat\ChatServer;

// use Cake\Filesystem\Folder;
// use Cake\Filesystem\File;



//use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime;
// use Cake\Core\Configure;
// use Cake\Event\Event;



class ChatsController extends AppController
{



    public function isAuthorized($user)
    {
        return true;
    }

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Token');
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $formaction = $this->request->getParam('action');
        $this->FormProtection->setConfig('unlockedActions', array(
            $formaction
        ));
        $this->Authentication->allowUnauthenticated(['uiregister', 'uiunregister', 'sendMessage', 'getcontact', 'loadchathistory', 'newchat', 'getNewmsg']);
    }

    function index()
    {
        // print(getenv('CHAT_URL'));
        $this->viewBuilder()->setLayout('ajax');
        $this->set('chat_url', getenv('CHAT_URL'));
    }


    public function createSession()
    {
        $this->autoRender = false; // Disable view rendering
        // Validate user ID and account ID
        $userId = $this->getMyUID();
        $accountId = $this->getMyAccountID();

        if (isset($userId) && isset($accountId)) {
            $data['user_id'] = $userId;
            $data['expiry'] = time() + 24 * 60 * 60; // 24 hours expiry
            $data['account_id'] = $accountId;

            $data['session_id'] = $this->Token->generateToken($data);
            $data['user_name'] = $this->getMyUserName();

            $this->setResponse(
                $this->response->withStatus(200) // OK status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Session created successfully',
                        'data' => $data
                    ]))
            );
        } else {
            // Return an error response if validation fails
            $this->setResponse(
                $this->response->withStatus(400) // Bad Request status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Invalid user or account'
                    ]))
            );
        }
    }


    public function uiunregister()
    {
        $this->request->allowMethod(['post']);
        // Use $this->request->getData() to retrieve POST data
        $data = $this->request->getData();

        // Ensure no view rendering for this action
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false; // Disable view rendering

        // Validate the token

        $ChatTable = $this->getTableLocator()->get('ChatsSessions');
        $record = $ChatTable->find()
            ->where(['clientid' => $data['client_id']])
            ->first();
        //   debug($record);
        if ($record && $ChatTable->delete($record)) {
            // Record deleted successfully
            $this->setResponse(
                $this->response->withStatus(200) // OK status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Session ' . $data['client_id'] . ' deleted successfully',
                        'data' => $record
                    ]))
            );
        } else {
            $this->setResponse(
                $this->response->withStatus(500) // OK status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Record not found to delete the session ' . $data['client_id']
                    ]))
            );
        }
    }

    public function uiregister()
    {
        $this->request->allowMethod(['post']);
        // Use $this->request->getData() to retrieve POST data
        $data = $this->request->getData();

        // Ensure no view rendering for this action
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false; // Disable view rendering

        // Validate the token
        $tokeninfo = $this->Token->validateToken($data['session_id']);
        if ($tokeninfo) {
            // Prepare to save the record
            $ChatTable = $this->getTableLocator()->get('ChatsSessions');
            $newRecord = $ChatTable->newEmptyEntity();
            $newRecord->clientid = $data['client_id'];
            $newRecord->account_id = $tokeninfo->account_id;
            $newRecord->user_id = $tokeninfo->sub;
            $newRecord->expiry = $tokeninfo->exp;
            $newRecord->token = $data['session_id'];

            if ($ChatTable->save($newRecord)) {
                // Success response
                $this->setResponse(
                    $this->response->withStatus(201) // Created status code
                        ->withType('application/json')
                        ->withStringBody(json_encode([
                            'message' => 'Record saved successfully',
                            'data' => $newRecord // Include saved data if needed
                        ]))
                );
            } else {
                // Failure response for save error
                $this->setResponse(
                    $this->response->withStatus(400) // Bad Request status code
                        ->withType('application/json')
                        ->withStringBody(json_encode([
                            'message' => 'Failed to save record',
                            'errors' => $newRecord->getErrors()
                        ]))
                );
            }
        } else {
            // Unauthorized response for invalid token
            $this->setResponse(
                $this->response->withStatus(401) // Unauthorized status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Invalid token information'
                    ]))
            );
        }
    }

    public function loadchathistory()
    {
        $this->request->allowMethod(['post']);
        $postData = $this->request->getData();
        $this->viewBuilder()->setLayout('ajax');
        $tokeninfo = $this->Token->validateToken($postData['session_id']);
        if ($tokeninfo) {
            //   debug($tokeninfo);
            $account_id = $tokeninfo->account_id;


            if(!isset($postData['page'])){
                $postData['page']=1;
            }



            $query = $this->getTableLocator()->get('Chats')->find();
            $query->where([
                'contact_stream_id' => $postData['contact_stream_id'],
                'account_id' => $account_id,
               // 'notified' => true //we will process notnotified message in diffrent request to avoid hug db change query to notified=true.  
            ]); //conditions.

            if(isset($postData['chat_id'])){
                $query->andWhere(['id'=>$postData['chat_id']]);
            }




            $query->andWhere(function ($exp, $q) {
                return $exp->or([
                    'sendarray IS NOT' => null,
                    'recievearray IS NOT' => null
                ]);
            });


           
            $query->select(['id', 'sendarray', 'recievearray', 'contact_stream_id', 'created','stream_id','type']);
            if (!isset($postData['direction'])) {
                $postData['direction'] = "up";
            }

            if (!isset($postData['page'])) { //bookmark show where to start the message from.
                if ($postData['direction'] == "up") { //scrooling up, old messages
                    $query->order(['modified' => 'DESC']);
                } else {
                    $query->order(['modified' => 'ASC']);
                }
            } else {
                $query->order(['id' => 'DESC']);
            }
            //   debug($query->sql();)
          //  $this->log('Contact list query result ' . json_encode($query->all()->toArray()), 'debug');

            $query->limit(50);
            $query->page($postData['page']);
            $messages = $query->all()->toArray();
            $this->set('messages', $messages);
            //    $this->set('contact_stream_id',$postData['contact_stream_id']);
        } else {

            $this->autoRender = false;
            $this->setResponse(
                $this->response->withStatus(201) // Created status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'error' => 'Wrong token'
                    ]))
            );
        }
        //    debug($query->sql());
    }





    public function getcontact()
    {

        $this->request->allowMethod(['post']);
        // Use $this->request->getData() to retrieve POST data
        $postData = $this->request->getData();

        // Ensure no view rendering for this action
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false; // Disable view rendering
        $tokeninfo = $this->Token->validateToken($postData['session_id']);
        if ($tokeninfo) {
            $account_id = $tokeninfo->account_id;


            // Subquery to get the latest chat ID for each contact_stream_id
    $subquery = $this->Chats->find()
    ->select(['latest_id' => 'MAX(id)'])
    ->where(['account_id' => $account_id])
    ->group(['contact_stream_id']);

// Main query to join the subquery with the chats table and contact_streams table
$query = $this->Chats->find()
    ->select([
        'Chats.id',
        'Chats.created',
        'Chats.contact_stream_id',
        'ContactStreams.profile_name',
        'ContactStreams.name',
        'ContactStreams.contact_number'
    ])
    ->join([
        'table' => 'contact_streams',
        'alias' => 'ContactStreams',
        'type' => 'INNER',
        'conditions' => 'Chats.contact_stream_id = ContactStreams.id',
    ])
    ->where([
        'Chats.id IN' => $subquery
    ])
    ->order(['Chats.id' => 'DESC']);



            if (isset($postData)) {
                $query->where([
                    'Chats.account_id' => $account_id,
                    'OR' => [
                        'ContactStreams.profile_name LIKE' => '%' . $postData['query'] . '%',
                        'ContactStreams.contact_number LIKE' => '%' . $postData['query'] . '%',
                    ],
                ]);
            }



            if (isset($postData['limit']) && isset($postData['page'])) {
                $query->limit((int) $postData['limit']);
                $query->page((int) $postData['page']);
            }

            $contact = $query->all();
            $this->setResponse(
                $this->response->withStatus(201) // Created status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Record saved successfully',
                        'data' => $contact // Include saved data if needed
                    ]))
            );
        } else {
            $this->setResponse(
                $this->response->withStatus(400) // Bad Request status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Failed to fech the data',
                        'errors' => "Wrong token info"
                    ]))
            );
        }
    }

    // function getNewmsg()
    // {
    //     $this->request->allowMethod(['post']);
    //     // Use $this->request->getData() to retrieve POST data
    //     $postData = $this->request->getData();

    //     //debug($postData);
    //     $this->log("Get new message with postdata, ".json_encode($postData), 'debug');
    //     $this->viewBuilder()->setLayout('ajax');


    //     // Validate the token
    //     $tokeninfo = $this->Token->validateToken($postData['session_id']);
    //     //  debug($tokeninfo);
    //     if ($tokeninfo) {
    //         $this->viewBuilder()->setLayout('ajax');
    //         $query = $this->getTableLocator()->get('Chats')->find()
    //             ->where([
    //                 'id' => $postData['chat_id']
    //             ])
    //             ->order(['Chats.id' => 'ASC']);
    //             $query->sql();
    //         $newMessages= $query->all()->toArray();
    //         $this->log("SQL is, ".$query->sql(), 'debug');

    //         $this->log("Data is, ". json_encode($query->toArray()), 'debug');
    //         $this->set('messages', $newMessages);

    //         $this->render('loadchathistory');
    //     } else {
    //         $this->autoRender = false;
    //         $this->setResponse(
    //             $this->response->withStatus(201) // Created status code
    //                 ->withType('application/json')
    //                 ->withStringBody(json_encode([
    //                     'error' => 'Wrong token'
    //                 ]))
    //         );
    //     }
    // }



    function newchat()
    {

        $this->request->allowMethod(['post']);
        // Use $this->request->getData() to retrieve POST data
        $postData = $this->request->getData();

        //debug($postData);

        $this->viewBuilder()->setLayout('ajax');


        // Validate the token
        $tokeninfo = $this->Token->validateToken($postData['session_id']);
        //  debug($tokeninfo);
        if ($tokeninfo) {
            $this->autoRender = false; // Disable view rendering
            $data['account_id'] = $tokeninfo->account_id;
            $FBSettings = $this->_getFBsettings($data);
            //   debug($FBSettings);
            if ($FBSettings['status']['code'] !== 200) {
                $this->setResponse(
                    $this->response->withStatus(201) // Created status code
                        ->withType('application/json')
                        ->withStringBody(json_encode([
                            'error' => 'Wrong API key of settings'
                        ]))
                );
            } else {
                switch ($postData['msgtype']) {
                    case "text":
                        $sendQData['contact_stream_id'] = $postData['contact_stream_id'];
                        $sendQData['type'] = $postData['msgtype'];
                        $sendQData['api_key'] = $this->getMyAPIKey($FBSettings['account_id']);
                        $sendQData['message'] = $postData['message'];
                        $sendQ = $this->getTableLocator()->get('SendQueues');
                        $sendQrow = $sendQ->newEmptyEntity();
                        $sendQrow->form_data = json_encode($sendQData);
                        $sendQrow->status = "queued";
                        $sendQrow->type = "chat";
                        //         $result = [];
                        $this->log('SendQueue entity before save: ' . json_encode($sendQrow), 'debug');
                        if ($sendQ->save($sendQrow)) {
                            $this->setResponse(
                                $this->response->withStatus(201) // Created status code
                                    ->withType('application/json')
                                    ->withStringBody(json_encode([
                                        'success' => "send queued " . $sendQrow->id
                                    ]))
                            );
                        } else {
                            $this->setResponse(
                                $this->response->withStatus(201) // Created status code
                                    ->withType('application/json')
                                    ->withStringBody(json_encode([
                                        'error' => "Failefd to Que msg"
                                    ]))
                            );
                        }
                        break;
                }
            }
        } else {
            $this->autoRender = false;
            $this->setResponse(
                $this->response->withStatus(201) // Created status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'error' => 'Wrong token'
                    ]))
            );
        }
    }
}
