<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Http\Client;

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
        $this->Authentication->allowUnauthenticated(['uiregister','uiunregister','sendMessage']);
    }

    function index()
    {
        $data['user_id'] = $this->getMyUID();
        $data['expiry'] = time() + 24 * 60; //24hrs expiry
        $data['account_id'] = $this->getMyAccountID();
        $this->set('session_id', $this->Token->generateToken($data));
        $this->set('user_name', $this->getMyUserName());
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

        $ChatTable = $this->getTableLocator()->get('Chats');
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
                        'message' => 'Session '. $data['client_id'].' deleted successfully',
                        'data'=> $record 
                    ]))
            );
        }else{
            $this->setResponse(
                $this->response->withStatus(500) // OK status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Record not found'
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
            $ChatTable = $this->getTableLocator()->get('Chats');
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


    // public function sendMessage()
    // {
    //     $this->request->allowMethod(['post']);
    //     $data = $this->request->getData();

    //     $clientId = $data['client_id'];
    //     $message = $data['message'];

    //      // Ensure the ChatServer is initialized with the loop on the first call
    //     $chatServer = \App\Chat\ChatServer::getInstance(); // Loop is initialized by the Shell command


    //     // Use a shared ChatServer instance to send the message
    //   //  $chatServer = $this->getChatServerInstance();
    //     $chatServer->sendMessageToClient($clientId, $message);

    //     $this->set([
    //         'message' => 'Message sent successfully',
    //         '_serialize' => ['message']
    //     ]);
    // }

    // private function getChatServerInstance()
    // {
    //     // Assume you have a way to get the ChatServer instance
    //     // This could be a singleton, a service container, etc.
    //     return \App\Chat\ChatServer::getInstance();
        
    // }


    public function sendMessage()
    {
        $this->request->allowMethod(['post']);
        $data = $this->request->getData();

        $clientId = $data['client_id'];
        $message = $data['message'];

        $chatServer = \App\Chat\ChatServer::getInstance();

        $chatServer->sendMessageToClient($clientId, $message);

        $this->set([
            'message' => 'Message sent successfully',
            '_serialize' => ['message']
        ]);
    }
}
