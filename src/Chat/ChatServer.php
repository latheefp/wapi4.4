<?php
//https://clouddevs.com/cakephp/real-time-chat-application/
namespace App\Chat; // Ensure correct namespace

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\Socket\Server as ReactServer;
use React\EventLoop\Timer\TimerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
use React\EventLoop\LoopInterface;
use Cake\Http\Client;

use Cake\Datasource\Exception\RecordNotFoundException;

use Cake\Log\LogTrait;



use Cake\Event\Event;
use Cake\ORM\TableRegistry;


class ChatServer implements MessageComponentInterface
{
    private static $instance = null;
    private $clients;
    private $loop;
    protected $lastPongTime;
    use LogTrait; // Include the LogTrait




    protected function __construct()

    {
        $this->clients = new \SplObjectStorage;
        $this->log("ChatServer initialized", 'debug');
    }




    public static function getInstance(LoopInterface $loop = null)
    {
        if (self::$instance === null) {
            self::$instance = new self();
            if ($loop !== null) {
                self::$instance->setLoop($loop);
            } else {
                throw new \Exception("LoopInterface is required for the first instance creation.");
            }
        }
        return self::$instance;
    }

    public function setLoop(LoopInterface $loop)
    {
        $this->loop = $loop;
        $this->log("LoopInterface set in ChatServer", 'debug');
    }


    public function run()
    {
        $webSock = new ReactServer('0.0.0.0:8080', $this->loop);
        $server = new IoServer(new HttpServer(new WsServer($this)), $webSock, $this->loop);
        echo "WebSocket server running with LoopInterface: " . get_class($this->loop) . "\n";


        // Schedule ping messages to be sent every 30 seconds
        $this->loop->addPeriodicTimer(30, function () {
            $this->sendPingMessages();
        });

        // Schedule a task to check for clients that didn't respond to the ping
        $this->loop->addPeriodicTimer(60, function () {
            $this->checkPongResponses();
        });
    }

    protected function sendPingMessages()
    {
        foreach ($this->clients as $client) {
            $client->send(json_encode(['type' => 'ping']));
            $this->lastPongTime[$client->resourceId] = time();
        }
    }

    public function getClients()
    {
        return $this->clients;
    }

    protected function checkPongResponses()
    {
        $currentTime = time();
        foreach ($this->clients as $client) {
            if(isset($this->lastPongTime[$client->resourceId])){
                if ($currentTime - $this->lastPongTime[$client->resourceId] > 60) {
                    echo "Client {$client->resourceId} did not respond to ping, disconnecting.\n";
                    $client->close();
                    $this->clients->detach($client);
                    $this->unregister($client->resourceId);
                    unset($this->lastPongTime[$client->resourceId]);
                }
            }
        }
    }
  

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) //reciveing message from clients. 
    {
        $data = json_decode($msg, true);

        


        $msgArray = json_decode($msg, true);
        $this->log("Message type ".$msgArray['type'] ." From ".$from->resourceId,'debug');
        $response = [
            'status' => 'success',
            'message' => 'Data submitted for '.$msgArray['type']
        ];
      //  print_r($msg);
        switch($msgArray['type']){
            case "register":
                $clientinfo['client_id'] = $from->resourceId;
                $clientinfo['session_id'] = $msgArray['session_id'];
                $this->registerclient($clientinfo);
                 
                break;
            case "loadcontact":
                $msgArray['client_id'] = $from->resourceId;
                $this->SendRecentChatContact($msgArray);
                break;
            case "loadChathistory":
            case  "appendchat":
                $msgArray['client_id'] = $from->resourceId;
             //  print_r($)
                $this->loadChathistory($msgArray);
                break;
            case "sendchat":
                $msgArray['client_id'] = $from->resourceId;
             //   print_r($msgArray);
                $this->newChat($msgArray);
                break;
           case "ProcessChatsID":
                    $msgArray['client_id'] = $from->resourceId;
                    $this->log("ProcessChatsID: is triggered on message", 'debug');
                    $this->ProcessChatsID($msgArray);
                    break;    
            default:

                print "No action defined for " . $msgArray['type'] . " Sending to Client";
                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        $client->send($msg);
                    }
                }
        }

        $from->send(json_encode($response));
        
    }

    function newChat($msgArray){
        $http = new Client();
        $response = $http->post('http://localhost/chats/newchat', $msgArray);
        $msgArray['html'] = $response->getStringBody();
        $this->sendMessageToClient($msgArray['client_id'], $msgArray);
    }

    function loadChathistory($query)
    {
        print_r($query);
        $http = new Client([
            'timeout' => 600 // Timeout in seconds
        ]);
        $response = $http->post('http://localhost/chats/loadchathistory', $query);
        $query['html'] = $response->getStringBody();
        $this->sendMessageToClient($query['client_id'], $query);
    }

    function SendRecentChatContact($query){ //function to send latest contact list 
        print "CAlling sendRcentContact function. ";
        $http = new Client([
            'timeout' => 600 // Timeout in seconds
        ]);
        $response = $http->post('http://localhost/chats/getcontact', $query);
        $message['type']="contactlist";
        $message['message']=json_decode( $response->getStringBody(),true);
        $this->sendMessageToClient($query['client_id'], $message);
       // print_r($response->getStringBody());
        print "Contact list finished";
    }

    public function sendMessageToClient($clientId, $message)
    {          
       // $clientMatch=false;
      
        
        foreach ($this->clients as $client) {
            if ($client->resourceId == $clientId) {
                print( "Send Msg: $client->resourceId");
             //   print "Sending message to  $clientId \n";
                $this->log("Sending message to  $clientId ", 'debug');
                $client->send(json_encode($message));
                $clientMatch=true;
            }
        }

     //   $this->log("Client Match is   $clientMatch ", 'debug');

    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        //echo "Connection {$conn->resourceId} has disconnected\n";
        $this->log("Connection {$conn->resourceId} has disconnected", 'debug');
        $this->unregister($conn->resourceId);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
        $this->unregister($conn->resourceId);
    }

    function logmsg($log, $des)
    {
      //  print ($des);
        $file =   '/var/www/html/logs/chat.log';
        $time = date("Y-m-d H:i:s", time());
        $handle = fopen($file, 'a') or die('Cannot open file:  ' . $file); //implicitly creates file
        fwrite($handle, print_r("\n$time============================= \n", true));
        fwrite($handle, print_r($log, true));
        fclose($handle);
       // print_r($log . "\n");
        //  print_r($from->resourceId);
    }


    public function pollDatabase()
    {

        $ChatsSessionsTable = TableRegistry::getTableLocator()->get('ChatsSessions');
        $activeSessions = $ChatsSessionsTable->find()->all();
        foreach ($activeSessions as $key => $val) {
            //$this->log("DB sesions " . $val->clientid, 'debug');
        }

        $active_clients = [];
        foreach ($this->clients as $client) {
          //  $this->log("Pinging Client " . $client->resourceId,  'debug');
            $active_clients[$client->resourceId] = $client->resourceId;
        }
        //print_r($active_clients);

        $ChatsSessionsTable = TableRegistry::getTableLocator()->get('ChatsSessions');
        $activeSessions = $ChatsSessionsTable->find()->all();
        foreach ($activeSessions as $key => $val) {
            if (!isset($active_clients[$val->clientid])) {
                $this->log("pollDatabase: Client is not active, deleting:  " . $val->clientid, 'debug');
                $this->unregister($val->clientid);
            }
        }

      //  print_r($this->getClients());
    }


    


    function ProcessChatsID($entity){
          $this->log('ProcessChatsID: called ProcessChatsID with chat ID:'.  $entity['id'], 'debug');

          $ChatssTable = TableRegistry::getTableLocator()->get('Chats');
          $id = $entity['id'];
          try {
              $data=$ChatssTable->get($id)->toArray();
          } catch (RecordNotFoundException $e) {
              // Handle the case where the record is not found
              $this->log("ProcessChatsID: Record with ID  $id  not found: " . $e->getMessage(), 'debug');
              return false;
             // echo "Error: Record not found.";
          } catch (Exception $e) {
              $this->log("ProcessChatsID: An error occurred: No Data found with key, $id  " . $e->getMessage(), 'debug');
              return false;
          }





           $ChatsSessionsTable = TableRegistry::getTableLocator()->get('ChatsSessions');
           $activeSessionsCount = $ChatsSessionsTable->find()
           ->where(['active' => 1, 'account_id' => $data['account_id']])
           ->count();
           
           if($activeSessionsCount == 0){
               $this->log("ProcessChatsID: Current active clients from db is zero, no action will be triggered.. $activeSessionsCount", 'debug');
               return false;
           }else{
               $this->log("ProcessChatsID: Current active clients from db. $activeSessionsCount", 'debug');
           }

           $activeSessions = $ChatsSessionsTable->find()
               ->where(['active' => 1, 'account_id' => $data['account_id']]);
               
           foreach ($activeSessions as $key => $val) {
               
               $query['type'] = "livechat";
               $query['session_id'] = $val->token;
               $query['contact_stream_id'] = $val->contact_stream_id;
               $query['client_id'] = $val->clientid;
               $query['chat-id'] = $entity['id'];
               $query['page'] = 1; //to be validated. make sure, it will not reset the page limit of chat history loaded by chat console.
               $this->log("ProcessChatsID: Preparing to Sendi Notifciation to matched client with query:" . json_encode($query), 'debug');
               $http = new Client([
                   'timeout' => 600 // Timeout in seconds
               ]);
               $response = $http->post('http://localhost/chats/loadchathistory', $query);
               $query['html'] = $response->getStringBody();
               if (empty($query['html'])) {
                   $this->log("ProcessChatsID: No new message for $val->account_id", 'debug');
               } else {
                   //  print "Sending message notification for account id $val->account_id with Client ID $val->clientid \n";
                   $this->log("ProcessChatsID: Sending message notification for account id $val->account_id with Client ID $val->clientid", 'debug');
                   $this->sendMessageToClient($query['client_id'], $query);
               }
               $client_match = true;
           }
       }
       



    function registerclient($clientinfo)
    {
       # print_r($clientinfo);
       $this->log("Registering  ".json_encode($clientinfo), 'debug');
       $http = new Client([
        'timeout' => 600 // Timeout in seconds
    ]);
        $response = $http->post('http://localhost/chats/uiregister', $clientinfo);
        $responseCode = $response->getStatusCode();
        if($responseCode==201){
            $this->logmsg($response->getStringBody(), null);
            $message['type']="register";
            $message['status']="success";
            $message['message']="Client Registered";
            $this->sendMessageToClient($clientinfo['client_id'],$message);
            $clientinfo['limit'] = 25;
            $clientinfo['page'] = 1;
            $clientinfo['query'] = null;
            print "Sending recent Chat";
            $this->SendRecentChatContact($clientinfo);
            
        }else{
            print "Something wrong while registering client. $responseCode is not 200";
           // print_r(json_decode($response->getStringBody(),true));
           $message['type']="register";
           $message['status']="failed";
            $message['message']="Client Registeration failed";
            $this->sendMessageToClient($clientinfo['client_id'],$message);
        }

    }

    function unregister($resourceId)
    {
        $this->log("unregister: Unregistering $resourceId", 'debug');
        $clientinfo['client_id'] = $resourceId;
        $http = new Client([
            'timeout' => 600 // Timeout in seconds
        ]);
        $response = $http->post('http://localhost/chats/uiunregister', $clientinfo);
     //   print_r($response);
       // $this->logmsg($response->getStringBody(), null);
    }



    
}
