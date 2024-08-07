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

use Cake\Log\LogTrait;



use Cake\Event\Event;
use Cake\ORM\TableRegistry;


class ChatServer implements MessageComponentInterface
{
    protected static $instance = null;
    protected $clients;
    protected $loop;
    protected $lastPongTime;
    use LogTrait; // Include the LogTrait


    public function __construct(LoopInterface $loop)
    {
        $this->clients = new \SplObjectStorage;
        $this->loop = $loop;
      //  echo "ChatServer initialized with LoopInterface: " . get_class($loop) . "\n";
        $this->log("ChatServer initialized with LoopInterface: " . get_class($loop), 'debug');
    }


    public static function getInstance(LoopInterface $loop = null)
    {
        if (self::$instance === null) {
            if ($loop === null) {
                throw new \Exception("LoopInterface is required for the first instance creation.");
            }
            self::$instance = new self($loop);
        }
        return self::$instance;
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

        $this->logmsg($msg, "first message from ".$from->resourceId);


        $msgArray = json_decode($msg, true);
        print_r($msg);
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
            default:

                print "No action defined for " . $msgArray['type'] . " Sending to Client";
                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        $client->send($msg);
                    }
                }
        }
        
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
        foreach ($this->clients as $client) {
            if ($client->resourceId == $clientId) {
                print "Sending message to  $clientId \n";
                $client->send(json_encode($message));
                break;
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
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
        $activeSessionsCount = $ChatsSessionsTable->find()
        ->where(['active' => 1, 'account_id' => $entity->account_id]);
      //  ->count();

        $this->log("Current Clients from poll DB". json_encode($this->clients), 'debug');
        
    }

    function notifyClients($entity){
        $this->log('The table in ChatServer is  : '. $entity, 'debug');
        $ChatsSessionsTable = TableRegistry::getTableLocator()->get('ChatsSessions');
        $activeSessionsCount = $ChatsSessionsTable->find()
        ->where(['active' => 1, 'account_id' => $entity->account_id])
        ->count();
        $this->log("Current active sessions from DB info $activeSessionsCount", 'debug');
        $activeSessions = $ChatsSessionsTable->find()
            ->where(['active' => 1, 'account_id' => $entity->account_id]);
        foreach ($activeSessions as $key => $val) {
            // $client_match=false;
            // $this->log("Current Clients ". json_encode($this->clients), 'debug');
            // foreach ($this->clients as $client) {
            //     $this->log(" Looping Client ID, $client against passed Client ID  $val->clientid", 'debug');
            // //    print($client->resourceId ."is the current ID, $val->clientid \n");
            //     if ($client->resourceId == $val->clientid ) {
                    $this->log("Sending Notifciation to matched client:" .  $val->clientid, 'debug');
                    $query['type']="livechat";
                    $query['session_id']=$val->token;
                    $query['contact_stream_id']=$entity->contact_stream_id;
                    $query['client_id']=$val->clientid;
                    $query['chat_id']=$entity->id;
                    $http = new Client([
                        'timeout' => 600 // Timeout in seconds
                    ]);
                    $response = $http->post('http://localhost/chats/getNewmsg', $query);
                  
                    $query['html'] = $response->getStringBody();
                    if(empty($query['html'])){
                       //  print "No new message for $val->account_id \n";
                         $this->log("No new message for $val->account_id" , 'debug');
                    }else{
                      //  print "Sending message notification for account id $val->account_id with Client ID $val->clientid \n";
                    $this->log("Sending message notification for account id $val->account_id with Client ID $val->clientid" , 'debug');
                    $this->sendMessageToClient($query['client_id'], $query);
                    }
                    $client_match=true;
                   
              //  }
          //  }  
            
            // if(!$client_match){
            //   //  print "$val->clientid is not active, unregistering \n";
            //     $this->log("$val->clientid is not active, unregistering" , 'debug');
            //   //  $this->unregister($val->clientid);
            // }
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
        $this->log("Unregistering $resourceId", 'debug');
        $clientinfo['client_id'] = $resourceId;
        $http = new Client([
            'timeout' => 600 // Timeout in seconds
        ]);
        $response = $http->post('http://localhost/chats/uiunregister', $clientinfo);
     //   print_r($response);
        $this->logmsg($response->getStringBody(), null);
    }



 
    
}
