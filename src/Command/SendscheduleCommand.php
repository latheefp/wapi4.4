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

 class SendscheduleCommand extends Command {

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
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

    public function initialize(): void {
        parent::initialize();
        $this->app = new AppController();
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        print("running with arg contact IDs => ". $args->getOption('contact_csv')." and  schedule id => ". $args->getOption('schedule_id')."\n");
        $schedule_id = $args->getOption('schedule_id');
        $contact_array=$this->form_contact_array($args->getOption('contact_csv'));
        debug($contact_array);
        $this->queue_message($contact_array,$schedule_id);
    }

    function form_contact_array( $contact_json) {
        $contact_array=[];
        $contact_contact_number_table = $this->getTableLocator()->get('ContactsContactNumbers');
        $contact_id = explode(",", $contact_json);
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
                    $contact_array[]=$val->_matchingData['ContactNumbers']['mobile_number'];
                }
            }
        }
        return array_unique($contact_array);
    }


    function queue_message($contact_array,$schedule_id){
        $sendarray=[];

        $schedTable = $this->getTableLocator()->get('Schedules');
        $schedQuery = $schedTable->find()
                ->where(['Schedules.id' => $schedule_id])
                ->select(['Campaigns.template_id', 'Schedules.name', 'Schedules.campaign_id', 'Schedules.user_id'])
                ->innerJoinWith('Campaigns')
                ->first();
        debug($schedQuery);      
        if(empty($schedQuery)){
            print "Empty Schedule info\n";
            return false;
        }  

        $sendarray['api_key']=$this->app->getMyAPIKey($schedQuery->user_id);
        $sendarray['schedule_name']=$schedQuery->name;

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
            debug($val);
            $component = [];
            $param = [];
            $field_name = $val['field_name'];
            $keyarray = explode("-", $field_name);
            if (($keyarray[0] == "file") && ($keyarray[2] == "header")) {  //its an image. 
                $headerarray['type'] = 'header';
                $headerarray['parameters'] = [];
                $component['type'] = $keyarray[2]; //header
                $param['type'] = $keyarray[3];
                if (isset($val['filename'])) {
                    $param[$keyarray[3]]['filename'] = $val['filename'];
                }
                $param[$keyarray[3]]['id'] = $val['fbimageid'];
                $headerarray['parameters'][] = $param;
                $sendarray['imageid']= $val['fbimageid'];
            }

            if ($keyarray[0] == "var") {  //parmeters injection. 
                $param['type'] = "text";
                $param['text'] = $val['field_value'];

                $bodyarray['parameters'][] = $param;
            }

            if ($keyarray[0] == "button") {  //parmeters for button variables. 
                $json = '{"type": "button", "sub_type": "url", "index": "0", "parameters": [{"type": "payload", "payload": "btntwo"}]}';
                $button_array = json_decode($json, true);
                $button_array['parameters'][0]['payload'] = $val['field_value'];
                $sendarray['template']['components'][] = $button_array;
            }
        }
    



      
        




      
        

    }

    function _sendms($sched_id) {
        // $app = new AppController();
        //  $app->constructClasses();
        $schedTable = $this->getTableLocator()->get('Schedules');
        $schedQuery = $schedTable->find()
                ->where(['Schedules.id' => $sched_id])
                ->select(['Campaigns.template_id', 'Schedules.name', 'Schedules.campaign_id', 'Schedules.user_id'])
                ->innerJoinWith('Campaigns')
                ->first();
        
        $sched_name = $schedQuery->name;
        $template_id = $schedQuery->_matchingData['Campaigns']['template_id'];
        $campaign_id = $schedQuery->campaign_id;
        $templatetable = $this->getTableLocator()->get('Templates');
        $templateQuery = $templatetable->find()
                ->where(['id' => $template_id])
                ->first();

        // debug($schedquery);

        $table = $this->getTableLocator()->get('ContactsSchedules');
        $csquery = $table->find();
        $csquery->where(['schedule_id' => $sched_id])
                ->all();

        $fbSettings = $data = $this->app->_getFBsettings(array('user_id' => $schedQuery->user_id));
        print_r($fbSettings);
        foreach ($csquery as $key => $val) {
            //   $data['mobile_number'] = $val->contact_waid;
	        sleep (1);
            print "Sending to ".+$val->contact_waid;
            $streams_table = $this->getTableLocator()->get('Streams');
            $streamrow = $streams_table->newEmptyEntity();
            $streamrow->schedule_id = $sched_id;
            $streamrow->contact_stream_id = $this->app->getWastreamsContactId($val->contact_waid, $fbSettings);
            $streamrow->initiator = "Web";
            $streamrow->type = "send";
            $streamrow->account_id = $fbSettings['account_id'];
            $streams_table->save($streamrow);
            $contact = $streams_table->get($streamrow->id);
            $table = $this->getTableLocator()->get('CampaignForms');
            $form = $table->find()
                    ->where(['campaign_id' => $campaign_id])
                    ->all();

            //     print_r($fbSettings);
//            debug($templateQuery);
//            print_r($contact);
//            print_r($form);
//            print_r($templateQuery);
              $result= $this->app->_despatch_msg($contact, $form, $templateQuery,$fbSettings);
              print_r($result);
                debug($result);
        }
    }

    // function _updateschedcontacts($id, $contact_json) {
    //     print("$contact_json");
    //   //  $contact_id = implode($contact_json, true);
    //  //   print_r($contact_id);
    //     $table = $this->getTableLocator()->get('ContactsSchedules');
    //     $table->deleteAll(['schedule_id' => $id]);
    //     $contact_contact_number_table = $this->getTableLocator()->get('ContactsContactNumbers');
    //     $contact_id = explode(",", $contact_json);
    //     print_r($contact_id);
    //     foreach ($contact_id as $ckey => $cval) {
    //         $query = $contact_contact_number_table->find()->innerJoinWith('ContactNumbers');
    //         $query->where(['contact_id' => $cval])
    //                 ->select([
    //                     'ContactsContactNumbers.contact_id',
    //                     'ContactsContactNumbers.contact_number_id',
    //                     'ContactNumbers.mobile_number',
    //                     'ContactNumbers.blocked'
    //                 ])
    //                 ->toArray();
    //         foreach ($query as $key => $val) {
    //             $blocked = $val->_matchingData['ContactNumbers']['blocked'];
    //             if ($blocked == false) {
    //                 // $row = $table->newEmptyEntity();
    //                 // $row->schedule_id = $id;
    //                 // $row->contact_waid = $val->_matchingData['ContactNumbers']['mobile_number'];
    //                 // print $val->_matchingData['ContactNumbers']['mobile_number']." is added \n";
    //                 // $table->save($row);
    //                 print( $val->_matchingData['ContactNumbers']['mobile_number']);
    //             }
    //         }
    //     }
    // }

}
