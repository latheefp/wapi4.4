<?php
//https://clouddevs.com/cakephp/real-time-chat-application/
namespace App\Chat; // Ensure correct namespace

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\Socket\Server as ReactServer;
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


    // public function __construct(LoopInterface $loop)
    // {
    //     $this->clients = new \SplObjectStorage;
    //     $this->loop = $loop;
    //     echo "ChatServer initialized with LoopInterface: " . get_class($loop) . "\n";
    // }

    public function __construct(LoopInterface $loop)
    {
        $this->clients = new \SplObjectStorage;
        $this->loop = $loop;
        echo "ChatServer initialized with LoopInterface: " . get_class($loop) . "\n";
    }

    // public static function getInstance(LoopInterface $loop = null)
    // {
    //     if (self::$instance === null) {
    //       //  print "Self instance is null";
    //         if ($loop === null) {
    //             throw new \Exception("LoopInterface is required for the first instance creation.");
    //         }
    //         self::$instance = new self($loop);
    //     }
    //     return self::$instance;
    // }
    // public function run()
    // {

    //     // Use ReactPHP socket server to create a non-blocking Ratchet server
    //     // $socket = new ReactServer('0.0.0.0:8080', $this->loop);
    //     // $httpServer = new HttpServer(new WsServer($this));
    //     // $server = new IoServer($httpServer, $socket, $this->loop);



    //     // $server = IoServer::factory(new HttpServer(new WsServer($this)), 8080, '0.0.0.0');
    //     // $server->run();

    //     $webSock = new ReactServer('0.0.0.0:8080', $this->loop);
    //     $server = new IoServer(new HttpServer(new WsServer($this)), $webSock, $this->loop);
    //     echo "WebSocket server running with LoopInterface: " . get_class($this->loop) . "\n";

    // }
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
    }
  

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) //reciveing message from clients. 
    {
        $data = json_decode($msg, true);

        $this->logmsg($msg, $from);


        $msgArray = json_decode($msg, true);
        if (isset($msgArray['type'])) {
            if ($msgArray['type'] == "register") {
                $clientinfo['client_id'] = $from->resourceId;
                $clientinfo['session_id'] = $msgArray['session_id'];
                $this->registerclient($clientinfo);
            }
        } else {
            // You can handle the received message here, such as saving it to the database
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send($msg);
                }
            }
        }
    }

    public function sendMessageToClient($clientId, $message)
    {
        foreach ($this->clients as $client) {
            if ($client->resourceId == $clientId) {
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

    function logmsg($log, $from)
    {
        $file =   '/var/www/html/logs/chat.log';
        $time = date("Y-m-d H:i:s", time());
        $handle = fopen($file, 'a') or die('Cannot open file:  ' . $file); //implicitly creates file
        fwrite($handle, print_r("\n$time============================= \n", true));
        fwrite($handle, print_r($log, true));
        fclose($handle);
        print_r($log . "\n");
        //  print_r($from->resourceId);
    }


    public function pollDatabase()
    {
        print "Polling DB \n";
        // Replace with your actual database query logic
        $newMessages = $this->getNewMessagesFromDatabase();

        print_r($newMessages);

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
        print_r($clientinfo);
        $http = new Client();
        $response = $http->post('http://localhost/chats/uiregister', $clientinfo);
        print_r($response);
        $this->logmsg($response->getStringBody(), null);
    }

    function unregister($resourceId)
    {
        $clientinfo['client_id'] = $resourceId;
        $http = new Client();
        $response = $http->post('http://localhost/chats/uiunregister', $clientinfo);
        print_r($response);
        $this->logmsg($response->getStringBody(), null);
    }



    
}
