<?php

namespace App\Controller;

use App\Controller\AppController;
// use Cake\Event\EventInterface;
// use App\Chat\ChatServer;
// use App\Service\ServiceContainer;
use WebSocket\Client;
// use Cake\Core\Configure;
// use Cake\Log\Log;
// use Queue\Model\Table\QueuedJobsTable;
use Cake\Event\EventInterface;
use Queue\Job\Job;
// use Cake\ORM\TableRegistry;
use Queue\Shell\Task\QueueTask;


// use App\Controller\AppController;
// use Cake\ORM\TableRegistry;
use Cake\Log\Log;




class TestsController extends AppController
{
   // protected $chatServer;


    
   public function beforeFilter(EventInterface $event): void
   {
       parent::beforeFilter($event);
       $this->FormProtection->setConfig('unlockedActions', ['add', 'newcamp', 'getcampaign', 'attachments', 'getschedules', 'newsched', 'getstreams', 'updatecomment', 'sendshedule']);
   }



   public function testq()
   {
       // Get the QueuedJobs table instance
       $queuedJobsTable = TableRegistry::getTableLocator()->get('Queue.QueuedJobs');
       
       // Job data
       $jobData = ['id' => 100];
       
       // Create job in the queue
       $result = $queuedJobsTable->createJob('ProcessChat', $jobData);
       
       // Log the result for debugging
       Log::debug('Job creation result: ' . json_encode($result));
   }

  //  public function initialize(): void
    // {
    //     parent::initialize();
    //     // Retrieve the ChatServer instance from the service container
    //     $this->chatServer = ServiceContainer::get('ChatServer');
    // }

    // public function chatserver()
    // {
    //     $this->viewBuilder()->setLayout('ajax');

    //     if ($this->chatServer !== null) {
    //         debug($this->chatServer->getClients());
    //     } else {
    //         $this->log("ChatServer instance is null.", 'error');
    //     }
    // }


    function senddata($id){
        try {
            $client = new Client("ws://localhost:8080");
            $data = json_encode(['type' => 'ProcessChatsID', 'id' => $id]);
            $client->send($data);
        
            $response = $client->receive();
            echo "Received response: $response\n";
        
        } catch (\Exception $e) {
            echo "Error: {$e->getMessage()}\n";
        }
    }

    function testws($id){
        $entityArray['id'] = $id;
        $entityArray['contact_stream_id'] = 1;
        $entityArray['account_id'] = 1;
        $entityArray['stream_id'] = 251566;
        $entityArray['type'] = 'ProcessChatsID';


        $data = json_encode($entityArray);
        $options = [
            'timeout' => 30 // Connection and read timeout in seconds
        ];
        $client = new Client(getenv('CHAT_INTERNAL_URL'), $options);
        $this->log("Calling websocket from Listener: ".getenv('CHAT_INTERNAL_URL') . " with data",'debug');
        $client->send($data);
        $response = $client->receive();
        $this->log("Received response: $response",'debug');
    }


    function testcommand()
    {
       // $this->log('ChatEventListener: detected changes in Chat table: ' . $event->getSubject()->getTable(), 'debug');
        $cmd = ROOT . "/bin/cake chat -a newchat -r " . 264144;
        $this->log("Testcontroller: Running $cmd", 'debug');
        exec("$cmd > /dev/null 2>&1 &");
        $this->log('Testcontroller: Command execution triggered.', 'debug');

        $logFile = ROOT . '/logs/command_output.log';
        exec("$cmd > $logFile 2>&1 &");
    }


    function tune(){
        $base_table = "stream_views";
        $fields = $this->_fieldtypes( $base_table);
        debug($fields);

    }

    function getstreams()
    {
        // Load the required tables
        $search="7237272";
        $streamsTable = $this->getTableLocator()->get('Streams');

        // Build the query with the necessary joins, ensuring proper aliasing and join order
        $query = $streamsTable->find()
            ->select([
                'Streams.lang',               // From Streams table
                'Streams.type',
                'Streams.message_from',
                'Streams.id',
                'Streams.account_id',
                'Schedules.name',             // From Schedules table
                'Campaigns.id',               // From Campaigns table
                'Campaigns.campaign_name',
                'ContactStreams.contact_number',  // From ContactStreams table
                'ContactStreams.profile_name',
                'ContactStreams.name',
            ])
            // First, join with Schedules
           // ->leftJoinWith('Schedules')

           ->leftJoin(
            ['Schedules' => 'schedules'],
            ['Schedules.id = Streams.schedule_id']  // Ensures Schedules is joined with Streams first
        )
            // Then, join with Campaigns, but through Schedules
            ->leftJoin(
                ['Campaigns' => 'campaigns'],
                ['Campaigns.id = Schedules.campaign_id']  // This ensures that Schedules is joined first
            )
            // Then, join with ContactStreams
            ->leftJoinWith('ContactStreams')
            // Apply where conditions
            ->where(['Streams.account_id' => 1])
            ->andWhere([
                'OR' => [
                    'ContactStreams.contact_number LIKE' => '%' . $search . '%',
                    'ContactStreams.profile_name LIKE' => '%' . $search . '%',
                    'ContactStreams.name LIKE' => '%' . $search . '%',
                    'Campaigns.campaign_name LIKE' => '%' . $search . '%',
                    'Schedules.name LIKE' => '%' . $search . '%',
                    'Streams.type LIKE' => '%' . $search . '%',
                    'Streams.message_from LIKE' => '%' . $search . '%',
                ]
            ])
            // Order by Streams.id in descending order
            ->order(['Streams.id' => 'DESC'])
            // Limit and offset
            ->limit(25)
            ->offset(0);


            $this->viewBuilder()->setLayout('ajax');
        //    $query = $this->_set_stream_query($this->request->getData(), $model, $base_table);
            //     debug($query);
            $data = $this->paginate = $query;
            $this->set('data', $this->paginate('Streams'));
        //    $this->set('fieldsType', $this->_fieldtypes($base_table));


    }

    function getstreams1()
    {
        $model = "StreamViews";
        $base_table = "stream_views";
        $this->viewBuilder()->setLayout('ajax');
        $query = $this->_set_stream_query($this->request->getData(), $model, $base_table);
        //     debug($query);
        $data = $this->paginate = $query;
        $this->set( 'data', $this->paginate($model));
        $this->set('fieldsType', $this->_fieldtypes($base_table));
    }

    public function _set_stream_query($querydata, $model, $base_table)
    {  //return array of quey based on passed values from index page search.
        $query = [
            'order' => [
                $model . '.id' => 'desc'
            ]
        ];

        //debug($querydata);
        if (isset($querydata['length'])) {
            $query['limit'] = intval($querydata['length']);
        } else {
            $query['limit'] = $this->_getsettings('pagination_count');
        }
        $fields = $this->_fieldtypes(table_name: $base_table);

   
        foreach ($fields as $title => $props) {
            if (($props['viewable'] == true)) {
                $query['fields']= $props['fld_name'];  //add only viewable field to searh.
                if(($props['searchable'] == true)){
                    if (isset($querydata['search']['value'])) {
                        $query['conditions']['OR'][] = array($model . "." . $props['fld_name'] . ' LIKE' => '%' . $querydata['search']['value'] . '%');
                    }
                }

            }
        }



        $start = intval($querydata['start']);
        $query['page'] = ($start / $query['limit']) + 1;
        if (!empty($querydata['columns'][$querydata['order']['0']['column']]['name'])) {
            $query['order'] = array($querydata['columns'][$querydata['order']['0']['column']]['name'] . ' ' . $querydata['order']['0']['dir']);
        }



        if ($querydata['show_recv'] == "true") {
            $query['conditions']['AND'][] = array($model . '.type' => 'receive');
        }

        //        $session = $this->request->getSession();
        $query['conditions']['AND'][] = array($model . ".account_id" => $this->getMyAccountID());

        return $query;
    }

    function uploadtest($id){
        $cmd = ROOT . DS . "bin/cake upload -i $id > /dev/null 2>&1 &";
        exec($cmd);
        debug($cmd);
    }

}
