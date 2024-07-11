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



class ChatServer implements MessageComponentInterface
{
    protected static $instance = null;
    protected $clients;
    protected $loop;
    protected $lastPongTime;



    public function __construct(LoopInterface $loop)
    {
        $this->clients = new \SplObjectStorage;
        $this->loop = $loop;
        echo "ChatServer initialized with LoopInterface: " . get_class($loop) . "\n";
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
                if($this->registerclient($clientinfo)){
                    $query['session_id']=$msgArray['session_id'];
                    $query['limit']=25;
                    $query['page']=1;
                    $query['client_id']=$from->resourceId;;;
                    $query['query']=null;
                   $this->SendRecentChatContact($query);
                }else{
                    print "Client registration failed";
                }
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
        $http = new Client();
        $response = $http->post('http://localhost/chats/loadchathistory', $query);
        $query['html'] = $response->getStringBody();
        $this->sendMessageToClient($query['client_id'], $query);
    }

    function SendRecentChatContact($query){ //function to send latest contact list 
        print "CAlling sendRcentContact function. ";
        $http = new Client();
        $response = $http->post('http://localhost/chats/getcontact', $query);
       // $this->logmsg($response->getStringBody(), "contact list for initial request");
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
      //  print "Polling DB \n";
        // Replace with your actual database query logic
        $newMessages = $this->getNewMessagesFromDatabase();

      //  print_r($newMessages);

        foreach ($newMessages as $message) {
            foreach ($this->clients as $client) {
                // Add logic to determine which clients should receive the message
                if ($this->shouldReceiveMessage($client, $message)) {
                    $client->send(json_encode($message));
                }
            }
        }
    }

    // Mock function to fetch new messages from the database
    protected function getNewMessagesFromDatabase()
    {
        // Replace with actual database fetch logic
        // return [
        //     ['user_id' => 2, 'content' => 'New message from DB2!'],
        //     ['user_id' => 3, 'content' => 'New message from DB!3'],
        //     ['user_id' => 5, 'content' => 'New message from DB!5'],
        //     ['user_id' => 6, 'content' => 'New message from DB!6']
        // ];
        return [];
    }

    // Mock function to determine if a client should receive the message
    protected function shouldReceiveMessage($client, $message)
    {
        // Add your logic here
        return true;
    }



    function registerclient($clientinfo)
    {
       # print_r($clientinfo);
        $http = new Client();
        $response = $http->post('http://localhost/chats/uiregister', $clientinfo);
        $responseCode = $response->getStatusCode();
        if($responseCode==201){
          //  print ("Response\n");
            //print_r(json_decode($response->getStringBody(),true));
            //print ("Response End\n");
            $this->logmsg($response->getStringBody(), null);
            $message['type']="success";
            $message['message']="Client Registered";
            $this->sendMessageToClient($clientinfo['client_id'],$message);
            return true;
        }else{
            print "Something wrong while registering client. $responseCode is not 200";
           // print_r(json_decode($response->getStringBody(),true));
            $message['type']="failed";
            $message['message']="Client Registeration failed";
            $this->sendMessageToClient($clientinfo['client_id'],$message);
            return false;
        }

    }

    function unregister($resourceId)
    {
        $clientinfo['client_id'] = $resourceId;
        $http = new Client();
        $response = $http->post('http://localhost/chats/uiunregister', $clientinfo);
     //   print_r($response);
        $this->logmsg($response->getStringBody(), null);
    }

 
    
}
