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
        $this->Authentication->allowUnauthenticated(['uiregister','uiunregister','sendMessage','getcontact']);
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



    public function getcontact($api_key) {

        $this->request->allowMethod(['post']);
        // Use $this->request->getData() to retrieve POST data
        $query = $this->request->getData();

        // Ensure no view rendering for this action
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false; // Disable view rendering

        // Validate the token
        $tokeninfo = $this->Token->validateToken($query['session_id']);
        if ($tokeninfo) {
            $account_id = $tokeninfo->account_id;
            $query = $this->getTableLocator()->get('RecentChats')->find();
            if (isset($get['query'])) {
                $query->where(
                    [
                        'AND' => ['account_id' => $account_id],
                        'OR' => [
                            'profile_name  LIKE' => '%' . $query['query'] . '%',
                            'contact_number  LIKE' => '%' . $query['query'] . '%',
                        ]
                    ]
                );
            }
            //        $query->andwhere(['account_id' => $account_id]);

            $query->limit((int) $query['limit']);
            $query->page((int) $query['page']);
            //  $query->group('contact_number');
            $contact = $query->all()->toArray();
            $this->setResponse(
                $this->response->withStatus(201) // Created status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Record saved successfully',
                        'data' => $contact // Include saved data if needed
                    ]))
            );
           
        }else{
            $this->setResponse(
                $this->response->withStatus(400) // Bad Request status code
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Failed to save record',
                        'errors' => $newRecord->getErrors()
                    ]))
            );
        }

       
    }


    function test($type = null)
    {
        switch ($type) {
            case register:
                break;
            case "getcontact":
                $http = new Client();
                $query['limit']=25;
                $query['page']=1;
                $query['client_id']=$from->resourceId;;
                $query['session_id']="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJBUFBfTkFNRSIsImF1ZCI6IldBSnVuY3Rpb25DaGF0IiwiaWF0IjoxNzE5NTcwMjEwLCJleHAiOjE3MTk2NTY2MTAsInN1YiI6MiwiYWNjb3VudF9pZCI6MX0.T_muGYXzoLU6cDYqRga2aB2F8wrmg4XTm0_b60M5uCU"
                $response = $http->post('http://localhost/chats/getcontact', $query);
                $this->set('response',$response);
                break;
        }
    }


}
