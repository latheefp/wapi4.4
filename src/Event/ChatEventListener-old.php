<?php
//src/Event/ChatEventListener.php
namespace App\Event;
use React\EventLoop\Factory;

use Cake\Event\EventListenerInterface;
use App\Chat\ChatServer;
use Cake\Log\LogTrait;
use WebSocket\Client;
use Cake\Log\Log;


use Cake\ORM\EntityInterface;
use Cake\Event\EventInterface;
use ArrayObject;
use Cake\ORM\TableRegistry;
use Queue\Model\Table\QueuedJobsTable;



class ChatEventListener implements EventListenerInterface
{
    use LogTrait; // Include the LogTrait
 //   use JobTrait; // Include JobTrait to dispatch jobs
    public function implementedEvents(): array
    {
        return [
            'Model.afterSave' => 'onAfterSave', //expect no edit happends to chats tables, only creation. 
         //   'Model.afterSaveCommit' => 'onAfterSaveCommit'
        ];
    }


    public function onAfterSave($event, $entity, $options)
    {
       // Log::debug("Triggering After save call in Listener.". $event->getSubject()->getTable());
        // Check if it's the specific table you are interested in
        if ($event->getSubject()->getTable() === 'chats') {
           // $this->log('ChatEventListener: detected changes in Chat table: ' . $event->getSubject()->getTable(), 'debug');
           // $cmd=ROOT ."/bin/cake chat -a newchat -r ". $entity->id . "-l ".getenv('CHAT_INTERNAL_URL') ;
         //   $this->log("ChatEventListener: Running $cmd", 'debug');
            // exec("$cmd > /dev/null 2>&1 &");
            // $this->log('ChatEventListener: Command execution triggered.', 'debug');

            // $logFile = ROOT . '/logs/command_output.log';
            // exec("$cmd > $logFile 2>&1 &");

          // Dispatch a job to process data after save

         $queuedJobsTable = TableRegistry::getTableLocator()->get('Queue.QueuedJobs');
         $jobData = ['id' => $entity->id];
         $queuedJobsTable->createJob('ProcessChat', $jobData);



            // $queuedJobsTable = new QueuedJobsTable();
            // $jobData = ['id' => $entity->id];
            // $queuedJobsTable->createJob('ProcessChatJob', $jobData);
            $this->log('ChatEventListener: Job dispatched for processing.', 'debug');
    
        }else{
          //  $this->log('The table is : ' . $event->getSubject()->getTable(), 'debug');
        }
    }


   // public function onAfterSaveCommit($event, $entity, $options)
//    public function onAfterSaveCommit(EventInterface $event, EntityInterface $entity, ArrayObject $options)
//    {
//        if ($event->getSubject()->getTable() === 'chats') {
//            $this->log('ChatEventListener: detected changes in Chat table (after save commit): ' . $event->getSubject()->getTable(), 'debug');
//            // Uncomment if needed
//            // $cmd = ROOT . "/bin/cake chat -a newchat -r " . $entity->id;
//            // $this->log("ChatEventListener: Running $cmd", 'debug');
//            // exec("$cmd > /dev/null 2>&1 &");
//            // $this->log('ChatEventListener: Command execution triggered.', 'debug');
//        }
//     }
}
