<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime; // Import FrozenTime
use App\Controller\AppController; //(path to your controller).
use Cake\Cache\Cache;

class MetricsCommand extends Command
{

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);



        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        

        if (intval(getenv('METRICSENABLED')) == true) {  //WebSocketEnabled in Env
             $io->out("METRICSENABLED Enabled, processing");
            while (true) {
                debug('Running metrics capturing');
                $this->updatemetrics();
                sleep (60);
            }
        }else{
            $io->out("METRICSENABLED is disabled");
            return Command::CODE_ERROR;
        }

    }

    public function initialize(): void
    {
        parent::initialize();
        $this->app = new AppController();
    }

    function updatemetrics()
    {


        // Retrieve the query result for the 'Accounts' table
        $accounts = $this->getTableLocator()->get('Accounts')->find()->all();

        // Extract IDs from the query result
        $ids = $accounts->extract('id')->toArray();
      //  debug($ids);

        foreach ($ids as $key => $account_id) {

            $templates = $this->getTableLocator()->get('Templates')->find()->where(['account_id' => $account_id]);
            $this->savemetric('template', $account_id, $templates->count());

            $campaigns = $this->getTableLocator()->get('CampaignViews')->find()->where(['account_id' => $account_id]);
            $this->savemetric('campaings', $account_id, $campaigns->count());


            $schedules = $this->getTableLocator()->get('ScheduleViews')->find()->where(['account_id' => $account_id]);
            $this->savemetric('schdules', $account_id, $schedules->count());

            $groups = $this->getTableLocator()->get('Contacts')->find()->where(['account_id' => $account_id]);
            $this->savemetric('groups', $account_id, $groups->count());



            $total_send = $this->getTableLocator()
                ->get('Streams')
                ->find()
                ->where(['account_id' => $account_id, 'type' => 'send'])
                ->count();
            $this->savemetric('total_send', $account_id, $total_send);

            $total_rcv = $this->getTableLocator()
                ->get('Streams')
                ->find()
                ->where(['account_id' => $account_id, 'type' => 'receive'])
                ->count();
            $this->savemetric('total_receive', $account_id, $total_rcv);

            $balance = $this->getTableLocator()->get('Accounts')->get($account_id);
            $this->savemetric('balance', $account_id, $balance->current_balance);


            $has_wa = $this->getTableLocator()
                ->get('Streams')
                ->find()
                ->where(['account_id' => $account_id, 'has_wa' => 1])
                ->count();

            if ($has_wa > 0) {
                $success_rate = $has_wa / $total_send  * 100;
            } else {
                $success_rate = 0;
            }

            $this->savemetric('success_rate', $account_id, $success_rate);
        }

        $groups = $this->getTableLocator()
                ->get('Contacts')
                ->find()
                ->where(['account_id' => $account_id])
                ->count();
            $this->savemetric('groups', $account_id, $groups);

            $contact_numbers=$this->getTableLocator()->get('ContactsContactNumbers')
            ->find()
            ->innerJoin(['Contacts' => 'contacts'], [
                'Contacts.id = ContactsContactNumbers.contact_id',
                'Contacts.account_id' => $account_id
            ])
            ->count();
            $this->savemetric('contact_numbers', $account_id, $contact_numbers);


        $RcvQueues = $this->getTableLocator()
            ->get('RcvQueues')
            ->find()
            ->where(['status' => 'queued'])
            ->count();
        $this->savemetric('RcvQueues', 0, $RcvQueues);
        $SendQueues = $this->getTableLocator()
            ->get('SendQueues')
            ->find()
            ->where(['status' => 'queued'])
            ->count();

            $this->savemetric('SendQueues', 0, $SendQueues);


    }

    function savemetric($module, $account_id=0, $value)
    {
        $metricTable = $this->getTableLocator()->get('Metrics');
        $newRow = $metricTable->newEmptyEntity();
        $newRow->module_name = $module;
        $newRow->metric_value = $value;
        $newRow->account = $account_id;
     //   debug($newRow);
        $metricTable->save($newRow);
    }


    // function cleanq(){
    //     $this->app = new AppController();
    //     //cleanSendQ
       
    //     $retentions=$this->app->_getsettings('q_retention');
    //     debug("Deleting Send Q older than $retentions");
    //     $sendTable=$this->getTableLocator()->get('SendQueues');
    //     $retentionsHoursAgo = FrozenTime::now()->subHours($retentions);
    //     $query = $sendTable->query();
    //     $query->delete()
    //     ->where([
    //         'http_response_code' => 200,
    //         'created <' => $retentionsHoursAgo
    //     ])
    //         ->execute();


    //     debug("Deleting Send Q older than $retentions");
    //     $rcvTable=$this->getTableLocator()->get('RcvQueues');
    //     $retentionsHoursAgo = FrozenTime::now()->subHours($retentions);
    //     $query = $rcvTable->query();
    //     $query->delete()
    //     ->where([
    //         'http_response_code' => 200,
    //         'created <' => $retentionsHoursAgo
    //     ])
    //         ->execute();

        
    //  }
 


}
