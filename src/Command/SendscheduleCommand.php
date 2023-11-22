<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\App;
use App\Controller\AppController; //(path to your controller).

/**
 * Sendschedule command.
 */

// /var/www/html/bin/cake Sendschedule -i 125 -c 6 
// /var/www/html/bin/cake Sendschedule -i 126 -c 6,7,19

class SendscheduleCommand extends Command
{

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        $parser->addOptions(
            [
                'schedule_id' => [
                    'short' => 'i',
                    'help' => 'The schedule ID',
                    'required' => true,
                ],
                'contact_csv' => [
                    'short' => 'c',
                    'help' => 'The Contact CSV',
                    'required' => true,
                ]
            ]
        );

        return $parser;
    }

    public function initialize(): void
    {
        parent::initialize();
        $this->app = new AppController();
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        print("running with arg contact IDs => " . $args->getOption('contact_csv') . " and  schedule id => " . $args->getOption('schedule_id') . "\n");
        $schedule_id = $args->getOption('schedule_id');
        $contact_array = $this->form_contact_array($args->getOption('contact_csv'));
        //     debug($contact_array);
        $this->app->writelog($contact_array);
        $this->queue_message($contact_array, $schedule_id);
    }

    function form_contact_array($contact_csv)
    {
        $contact_array = [];
        $contact_contact_number_table = $this->getTableLocator()->get('ContactsContactNumbers');
        $contact_id = explode(",", $contact_csv);
        print_r($contact_id);
        foreach ($contact_id as $ckey => $cval) {
            $query = $contact_contact_number_table->find()->innerJoinWith('ContactNumbers');
            $query->where(['contact_id' => $cval]) 
                ->select([
                    'ContactsContactNumbers.contact_id',
                    'ContactsContactNumbers.contact_number_id',
                    'ContactNumbers.mobile_number',
                    'ContactNumbers.blocked'
                ])
                ->toArray();
            foreach ($query as $key => $val) {
                $blocked = $val->_matchingData['ContactNumbers']['blocked'];
                if ($blocked == false) {
                    $contact_array[] = $val->_matchingData['ContactNumbers']['mobile_number'];
                }
            }
        }
        return array_unique($contact_array);
    }


    function queue_message($contact_array, $schedule_id)
    {
        $sendarray = [];

        $schedTable = $this->getTableLocator()->get('Schedules');
        $schedQuery = $schedTable->find()
            ->where(['Schedules.id' => $schedule_id])
            ->select(['Campaigns.template_id', 'Schedules.name', 'Schedules.campaign_id', 'Schedules.user_id','Schedules.account_id'])
            ->innerJoinWith('Campaigns')
            ->first();
       //    debug($schedQuery);      
        if (empty($schedQuery)) {
            print "Empty Schedule info\n";
            return false;
        }

        //debug($schedQuery->account_id);

        $sendarray['api_key'] = $this->app->getMyAPIKey($schedQuery->account_id);
        $sendarray['schedule_name'] = $schedQuery->name;

        $template_id = $schedQuery->_matchingData['Campaigns']['template_id'];
        $campaign_id = $schedQuery->campaign_id;
        $templatetable = $this->getTableLocator()->get('Templates');
        $templateQuery = $templatetable->find()
            ->where(['id' => $template_id])
            ->first();
        //  debug($templateQuery);

        $CampaignFormstable = $this->getTableLocator()->get('CampaignForms');
        $form = $CampaignFormstable->find()
            ->where(['campaign_id' => $campaign_id])
            ->all();



        foreach ($form as $key => $val) {
       //     debug($val);
            $component = [];
            $param = [];
            $field_name = $val['field_name'];
            $keyarray = explode("-", $field_name);
         //   debug($keyarray);
            if (($keyarray[0] == "file") && ($keyarray[2] == "header")) {  //its an image. 
                if (isset($val['filename'])) {
                    $sendarray['filename'] = $val['filename'];
                }
                $sendarray['imageid'] = $val['fbimageid'];
            }

            if ($keyarray[0] == "var") {  //parmeters injection. 
                $sendarray['var-' . $keyarray[1]] = $val['field_value'];
            }

            if ($keyarray[0] == "button") {  //parmeters for button variables. 
                $sendarray['button_var'] = $val['field_value'];
            }
        }
        foreach ($contact_array as $contact_id => $contact_number) {
            $sendarray['mobile_number']= $contact_number;
            debug($sendarray);
            $json = json_encode($sendarray);
            $sendTable = $this->getTableLocator()->get('SendQueues');
            $newsendQ = $sendTable->newEmptyEntity();
            $newsendQ->form_data = $json;
            $newsendQ->type = "camp";
            $newsendQ->status = "queued";
            $sendTable->save($newsendQ);
        }
    }
}
