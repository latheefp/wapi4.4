<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Hash;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use Cake\Event\Event;
use DateTime;
use Cake\Mailer\Mailer;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\ServerRequest;
use Cake\Cache\Cache;
use Cake\Datasource\Exception\RecordNotFoundException;

use function PHPUnit\Framework\isEmpty;

/**
 * 
 * 
 * Apis Controller
 *
 * @method \App\Model\Entity\Api[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JobsController extends AppController
{

    public function isAuthorized($user)
    {
        return true;
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $formaction = $this->request->getParam('action');

        $this->FormProtection->setConfig('unlockedActions', array(
            $formaction
        ));


        $this->Authentication->allowUnauthenticated(['runjob', 'sendcamp']);
    }


    public function runjob()
    {
        $return = array();
        $this->viewBuilder()->setLayout('ajax');
        $apiKey = $this->request->getHeaderLine('X-Api-Key');
        $type = $this->request->getData('type');
        $qid = $this->request->getData('qid'); // Assuming this is a POST request
        //   debug($type);


        $FBSettings = $this->_getFBsettings($data = ['api_key' => $apiKey]); //This FB settings are just make sure, the paswed API key is valid before processing.
        if ($FBSettings['status']['code'] == 404) {
            $this->response = $this->response->withStatus(401); // Unauthorized
            $this->_update_http_code($qid, '404', $type);
            $response['error'] = 'Invalid qid APIKEY';
            $this->set('response', $response);
            return;
        }



        if (!is_numeric($qid) || empty($qid)) {
            $this->response = $this->response->withStatus(400); // Bad Request
            $this->_update_http_code($qid, '400', $type);
            http_response_code(400); // Bad Request
            $response['error'] = "Invalid qid not numbr or empty  $qid";
            $this->set('response', $response);
            return;
        }

        // debug($type);s
        switch ($type) {
            case "send":  //message to be send to end user.  


                //check record exists in SendQ Table.
                try {
                    $table = TableRegistry::getTableLocator()->get('SendQueues');
                    $sendQrecord = $table->get($qid);
                } catch (RecordNotFoundException $exception) {
                    $this->_update_http_code($qid, '404', $type);
                    $response['error'] = "Record not found:".$exception->getMessage();
                    $this->set('response', $response);
                    return;
                }



                //Validate API key of the Json array in SendForm.
                $form_data = json_decode($sendQrecord->form_data, true);
                $FBSettings = $this->_getFBsettings($data = ['api_key' => $form_data['api_key']]);
                if ($FBSettings['status']['code'] == 404) {
                    $this->response = $this->response->withStatus(401); // Unauthorized
                    $this->_update_http_code($qid, '404', $type);
                    $response['error'] = 'Invalid qid APIKEY';
                    $this->set('response', $response);
                    return;
                }

           //     debug($sendQrecord);
                switch($sendQrecord->type){
                    case "forward":
                        $return = $this->_forwardmsg($sendQrecord, $FBSettings);
                        break;
                
                    case "chat":
                        $return = $this->_chat($sendQrecord, $FBSettings);
                        break;
                    default:
                    $return = $this->_send_schedule($qid, $FBSettings); //main send fuction to fb api.
                    break;
                }

               
                if (isset($return['result']['error'])) {
                    // debug($return);
                    $this->_update_http_code($qid, '403', $type); //forbidden
                    $this->response = $this->response->withStatus(403); // forbidden
                    $response['success'] = 'Failed';
                } elseif (isset($return['result']['messages'][0]['id'])) {
                    //  debug($return);
                    $this->_update_http_code($qid, '200', $type); //success
                    $this->_update_http_code($qid, '200', $type);  //success
                } else {
                    $this->_update_http_code($qid, '500', $type);   //application erorr
                    $this->_update_http_code($qid, '500', $type); //application erorr
                }
                break;
            case "receive":
                $return = $this->processrcv($qid);
                // debug($return);
                if (isset($return['status'])) {
                    //   debug($return);
                    $return = $this->_update_status($return,$FBSettings);
                    $return['pricingResult']=$this->ratingApi($qid,$apiKey);
                    http_response_code(200); // Good Request
                    $this->_update_http_code($qid, '200', $type);
                    $this->set('response', $return);
                } else {
                    $this->_update_http_code($qid, '200', $type);
                    // $return['result']['status'] = 'Success';
                }

                break;
                default:
                    $return['error'] = "Invalid qid request type $type";
                    $return['result'] = "Invalid qid request type $type";
                    $this->response = $this->response->withStatus(500); // Unauthorized
                    $this->set('response', $return);
                break;

        }
        //      debug($return);
        $this->set('response', $return['result']);
    }


    function _chat($sendQrecord, $FBsettings)
    { //this function to procecess chat Q send. 
        $retun=[];
        $result=[];
        $form_data = json_decode($sendQrecord->form_data, true);
        switch ($form_data['type']) {
            case "text":
                $streams_table = $this->getTableLocator()->get('Streams');
                $streamrow = $streams_table->newEmptyEntity();
                $streamrow->contact_stream_id = $form_data['contact_stream_id'];
                $streamrow->initiator = "Chat";
                $streamrow->type = "Chat";
                $streamrow->postdata = $sendQrecord->form_data;
                $streamrow->account_id = $FBsettings['account_id'];
                $streams_table->save($streamrow);
                $contact = $streams_table->get($streamrow->id);
                $result = $this->_despatch_msg($contact,$form_data, null, $FBsettings, "text");
                if (isset($result['messages'][0]['id'])) {
                    $return['result']['status'] = "success";
                    $return['result']['msg'] = $result;
                } else {
                    $return['result']['status'] = "failed";
                    $return['result']['msg'] =$result;
                }
                break;
            case "image":
           
                break;
            case "document":
         
                break;
            case "video":
        
                break;
       
            case "location":
              
                break;
            case "sticker":
                
                break;
            case "audio":
                
                break;
            case "reaction":
              
                break;
            case "contacts":
                
                break;
        }

        return $return;
    
    }


    function _forwardmsg($sendQrecord, $FBsettings)
    { //this function to create  stream 
        $retun=[];
        $form_data = json_decode($sendQrecord->form_data, true);

        
       

        $streamsTable = $this->getTableLocator()->get('Streams');
        if ($streamsTable->exists(['id' => $form_data['stream_id']])) {
            $streams = $streamsTable->get($form_data['stream_id']);
        } else {
            $return['result']['error'] = "Invalid  stream id.";
            return $return;
        }
     //   debug($streams);

        switch ($streams->type) {
            case "send":
           //     debug("Send");
                //currently we are proceesing only reciv msg.
                break;
            case "receive":
             //   debug("Recieve");
                $msgArray = json_decode($streams->recievearray, true);
                $message = $msgArray['entry'][0]['changes'][0]['value']['messages'][0];
                $sender=$msgArray['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
                $sender_profile=$msgArray['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'];
                $sendarrayJson = '{
                    "messaging_product": "whatsapp",
                    "recipient_type": "individual"
                }';
                $sendarray = json_decode($sendarrayJson, true);
            //    debug($message);
                $type = $message['type'];
                $sendarray['type'] = $type;
                $sendarray['to'] = $form_data['mobile_number'];
                $payload = [];
                // debug($sendarray);
                //  debug($type);
                // debug($message);
               // debug($message[$type]['id']);
               if(isset($message[$type]['caption'])){
                $caption=$message[$type]['caption'] ." from $sender_profile($sender)";
               }else{
                $caption="from $sender_profile($sender)";
               }
                switch ($type) {
                    case "image":
                        $payload['id'] = $message[$type]['id'];
                        $payload['caption'] =  $caption;
                        break;
                    case "document":
                        $payload['id'] = $message[$type]['id'];
                        $payload['caption'] =  $caption;
                        break;
                    case "video":
                        $payload['id'] = $message[$type]['id'];
                        $payload['caption'] =  $caption;
                        break;
                    case "text":
                        $payload['body'] = $message[$type]['body'];
                        break;
                    case "location":
                        $payload = $message[$type];
                        break;
                    case "sticker":
                        $payload['id'] = $message[$type]['id'];
                        break;
                    // case "interactive":  //dont Foward interactive.
                    //     $payload['id'] = $message[$type]['id'];
                    //     break;
                    case "audio":
  
                        $payload['id'] = $message[$type]['id'];

                        break;
                    case "reaction":
                        $payload = $message[$type];
                        break;
                    case "contacts":
                        $payload = $message[$type];
                        break;
                }
                $sendarray[$type] = $payload;
            //    debug($sendarray);
                break;
        }

        //  debug($FBsettings);
        if (isset($sendarray[$type])) {
            $streams_table = $this->getTableLocator()->get('Streams');
            $streamrow = $streams_table->newEmptyEntity();
            //   $streamrow->schedule_id = $sched_id;
            $streamrow->contact_stream_id = $this->getWastreamsContactId($form_data['mobile_number'], $FBsettings);
            $streamrow->initiator = "API";
            $streamrow->type = "forward";
            $streamrow->postdata = json_encode($sendarray);
            $streamrow->account_id = $FBsettings['account_id'];
            if (!$streams_table->save($streamrow)) {
                debug($streamrow);
                debug($streamrow->getErrors()); // Print save errors
                $return['result']['error'] = "Failed to upate streams";
                return $return;
            }
            //  return false;

            //   debug($streamrow);
            $contact = $streams_table->get($streamrow->id);
            $templateQuery = [];
            $return['result'] = $this->_despatch_msg($contact, $sendarray, $templateQuery, $FBsettings, $type = "forward"); 
            //  debug($return);

        } else {
            $return['result'] = array("message" => "Nothing to forward for type $type");
        }
       
      return $return;
       
    }

 


    

    function _update_http_code($qid, $code, $type)
    {

        switch ($type) {
            case 'send':
              //  $table = TableRegistry::getTableLocator()->get('SendQueues');
                try {
                    $table = TableRegistry::getTableLocator()->get('SendQueues');
                    $row = $table->get($qid);
                } catch (RecordNotFoundException $exception) {
                    $return['result']['status'] = "failed";
                    $return['result']['message'] =  "Record not found:".$exception->getMessage();
                    return $return;
                }
               
                $row->http_response_code = $code;
                $row->processed = 1;
                $row->status = "processed";
                $table->save($row);
                break;
            case "receive":

                try {
                    $table = TableRegistry::getTableLocator()->get('RcvQueues');
                    $row = $table->get($qid);
                } catch (RecordNotFoundException $exception) {
                    $return['result']['status'] = "failed";
                    $return['result']['message'] =  "Record not found:".$exception->getMessage();
                    return $return;
                }
                // $table = TableRegistry::getTableLocator()->get('RcvQueues');
                // $row = $table->get($qid);
                $row->http_response_code = $code;
                $row->processed = 1;
                $row->status = "processed";
                $table->save($row);
                break;
            case "camp": //for Campagn.
                try {
                    $table = TableRegistry::getTableLocator()->get('Schedules');
                    $row = $table->get($qid);
                } catch (RecordNotFoundException $exception) {
                    $return['result']['status'] = "failed";
                    $return['result']['message'] =  "Record not found:".$exception->getMessage();
                    return $return;
                }
             //   $table = TableRegistry::getTableLocator()->get('Schedules');
                $row = $table->get($qid);
                $row->http_response_code = $code;
                $table->save($row);
        }
    }




//send job start here.
    function _send_schedule($qid, $FBSettings) //send scheduled message from sendq. this is the main function for delivering message.
    {
        $return = array();
        $Qtable = TableRegistry::getTableLocator()->get('SendQueues');
        $record = $Qtable->get($qid);

        // $this->viewBuilder()->setLayout('ajax');
        $this->writelog("Whatsapp Schedule function hit", null);
        $data = json_decode($record->form_data, true);
    //    debug($data);
        if(!isset($data['contact_stream_id'])){ //chat client provide contact_stream_id instead of mobile number.
            if (!isset($data['mobile_number'])) {
                $return['result']['error'] = "No mobile number provided";
                return $return;
            }
            if (strlen($data['mobile_number']) >= 10 && is_numeric($data['mobile_number'])) {
                // echo "Valid mobile number!";
            } else {
                $return['result']['error'] = "Invalid mobile number ".$data['mobile_number'];
                return $return;
            }
        }


        //Logic for ERPNext OTP
        if(isset($data['type']) && $data['type']=="erpnextotp"){
            $this->writelog($data, "Processing erpnextotp data from _send_scheduel function ".$data['var-1']);
            $data['var-1'] = str_replace('Your verification code is ', '', $data['var-1']);
        }
        // if($data['type']=="erpnextotp"){
        //     $this->writelog($data, "Processing erpnextotp data from _send_scheduel function ".$data['var-1']);
        //     $data['var-1'] = str_replace('Your verification code is ', '', $data['var-1']);
        // }


        $this->writelog($data, "Processing shedule data from _send_scheduel function");
        $schedTable = $this->getTableLocator()->get('Schedules');
        //TODO: add account ID also in query to make sure, we catch exact schedule the account. 
        $schedQuery = $schedTable->find()
            ->where(['Schedules.name' => $data['schedule_name']])
            ->select(['Campaigns.template_id', 'Schedules.campaign_id', 'id'])
            ->innerJoinWith('Campaigns')
            ->first();
        if (empty($schedQuery)) {
            $return['result']['error'] = "No matching schedule found, " . $data['schedule_name'];
            $this->writelog($schedQuery, "Shedule query result is empty, no matching schedule name");
            return $return;
        } else {
            $this->writelog($schedQuery, "Found schedule " . $data['schedule_name'] . " in table");
            $sched_id = $schedQuery->id;
           //     debug($schedQuery);
            //!!Do the related updates in console as well. 
            $streams_table = $this->getTableLocator()->get('Streams');
            $streamrow = $streams_table->newEmptyEntity();
            $streamrow->schedule_id = $sched_id;
            $streamrow->contact_stream_id = $this->getWastreamsContactId($data['mobile_number'], $FBSettings);
            $streamrow->initiator = "API";
            $streamrow->type = $record->type;
            $streamrow->postdata = json_encode($data);
            $streamrow->account_id = $FBSettings['account_id'];
            if (!$streams_table->save($streamrow)) {
                debug($streamrow);
                debug($streamrow->getErrors()); // Print save errors
                $return['result']['error'] = "Failed to upate streams";
                return $return;
            }
            $contact = $streams_table->get($streamrow->id);
            // debug($contact);
            $template_id = $schedQuery->_matchingData['Campaigns']['template_id'];
            //     debug($template_id);
            $templatetable = $this->getTableLocator()->get('Templates');
            $templateQuery = $templatetable->find()
                ->where(['id' => $template_id])
                ->first();

            $campaign_id = $schedQuery->campaign_id;
            $table = $this->getTableLocator()->get('CampaignForms');
            $CampaignForm = $table->find()
                ->where(['campaign_id' => $campaign_id])
                ->all();

            $formarray = [];
          //  debug($data);
           // debug($CampaignForm);
            foreach ($CampaignForm as $key => $val) {
                $newval = array();
                $vararray = explode('-', $val['field_name']);
             //   debug($vararray);
                $newval['field_name'] = $val->field_name;
                $newval['field_value'] = $val->field_value;
                switch ($vararray[0]) {
                    case "file":
                        if (isset($data['media_id'])) {
                            $newval['fbimageid'] = $data['media_id'];
                            if (isset($data['filename'])) {
                                $newval['filename'] = $data['filename'];
                            }
                        }
                        break;

                        //replace the form vairables from campaign table with whats is submitted by api    
                    case "var":
                        $newvar = $vararray[0] . "-" . $vararray[1];
                        if (isset($data[$newvar])) {
                            $newval['field_value'] = $this->clearText($data[$newvar]); //cleartext will remove more than one space and newline on varaibles.
                        }
                        break;
                    case "button":
                     //   debug("Button");
                    //    debug($data);
                        $newvar = "button_var"; 
                      //  debug($data[$newvar]);
                       
                        if (isset($data[$newvar])) {
                            $newval['field_value'] = $this->clearText($data[$newvar]);//cleartext will remove more than one space and newline for button text
                        }
                        break;
                }
                $formarray[] = $newval;
            }

       //     debug($formarray);
            $return['result'] = $this->_despatch_msg($contact, $formarray, $templateQuery, $FBSettings,'template');
            return $return;
        }
    }




    public function processrcv($id)
    {
        // $this->viewBuilder->setLayout('ajax');
        $return['result'] = [];
        try {
            $Qtable = TableRegistry::getTableLocator()->get('RcvQueues');
            $record = $Qtable->get($id);
        } catch (RecordNotFoundException $exception) {
            $return['result']['status'] = "failed";
            $return['result']['message'] =  "Record not found:".$exception->getMessage();
            return $return;
        }

        // debug(getenv('LOG'));
        $input = json_decode($record->json, true);

       


        $this->writelog($input, "Post Data from Process Job");


        $dataarray['hookid'] = $input['entry'][0]['id'];
        $dataarray['messaging_product'] = $input['entry'][0]['changes'][0]['value']['messaging_product'];
        $phone_numberId = $input['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'];
        $FBSettings = $this->_getFBsettings(['phone_numberId' => $phone_numberId]);
        if ($FBSettings['status']['code'] != 200) {
            $record->status = $FBSettings['status']['message'];
            $Qtable->save($record);

            $return['result']['status'] = "failed";
            $return['result']['message'] = "No account related to phone_numberId $phone_numberId";
            return $return;
        }
        //   debug($FBSettings);
        $dataarray['account_id'] = $FBSettings['account_id'];
        $this->writelog($FBSettings, "FB settings");
        $display_phone_number = $input['entry'][0]['changes'][0]['value']['metadata']['display_phone_number'];
        $dataarray['display_phone_number'] = $display_phone_number;
        $dataarray['phonenumberid'] = $phone_numberId;
        // $sender = $input['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
        // $dataarray['contact_waid'] = $sender;
        if (isset($input['entry'][0]['changes'][0]['value']['messages'])) { //type is message
            $sender = $input['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
            $dataarray['contact_waid'] = $sender;

            $message = $input['entry'][0]['changes'][0]['value']['messages'][0];
            $dataarray['recievearray'] = $record->json;
            // debug($message);
            $messageid = $message['id'];
            $this->writelog($messageid, "Picked up by message");
            $dataarray['messageid'] = $messageid;
            $dataarray['message_format_type'] = $message['type'];
            if (isset($dataarray['message_context'])) {
                //TODO: fix message context.
                $dataarray['message_context'] = "no idea what is needed here.";
            }
            $msgtype = $dataarray['message_format_type'];
            $return['result']['msg_type'] = $msgtype;
            $adminforward=true;
            $isCmd=false;
            // debug($input);
            // debug($FBSettings);
            switch ($msgtype) {
                case "text":
                    $dataarray['message_txt_body'] = $message['text']['body'];
                    $isCmd=$this->processCMd($dataarray, $FBSettings);  //process CMD and set cmd is true or false.
                    //if cme is true, we will use this status to skip this msg forwarding.  
                    break;
                case "button":
                    $dataarray['button_payload'] = $message['button']['payload'];
                    $dataarray['button_text'] = $message['button']['text'];
                    break;
                case "document":
                    break;
                case "sticker":
                    break;
                case "unknown":
                    break;
                case "contacts":
                    break;
                case "video":
                    break;
                case "image":
                    break;
                case "request_welcome":  // Indicates first time message from WhatsApp user
                    $return['result']['request_welcome']=$this->request_welcome($dataarray, $FBSettings); // FB inbuild welcome msg
                    break;    
                case "interactive":
                    $adminforward=false;
                    $this->_processInteractive($record->json, $FBSettings);
                    $this->readmsg($messageid, $FBSettings); //existing interactive communcatoin. 
                    break;
            }
//            debug($input);
            $dataarray['delivered_time'] = date("Y-m-d h:i:s", time());
            $dataarray['type'] = "receive";
            $this->writelog($message, "message");
            
            $this->writelog($sender, "is the sender");
            $dataarray['message_timestamp'] = $this->_formate_date($input['entry'][0]['changes'][0]['value']['messages'][0]['timestamp']);
            $dataarray['contacts_profile_name'] = $input['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'];
           
            $Timeout = $this->_checktimeout($dataarray['contact_waid']); //dont move this function from here , it should be 
          //  debug($input);
            if (isset($input['entry'][0]['changes'][0]['value']['messages'][0]['context']['from'])) {  //reply of existing msg
              //  debug("Reply");
                $return['result']['msg_context'] = "reply";
                $return['result']['status'] = "success";
                $return['result']['message'] = "Not charged for reply";
                $dataarray['message_context'] = "reply";
                $dataarray['message_contextId'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['id'];
                $dataarray['message_context_rom'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['from'];
                $this->writelog($dataarray, "Save data for new Reply message");
                //    $save_status = $this->_savedata($dataarray, $FBSettings);  // no default reply needed for 
            } elseif(isset($input['entry'][0]['changes'][0]['value']['messages'][0]['context']['forwarded'])){ //Forwarded msg.
          //      debug("Forward");
                $return['result']['msg_context'] = "Forward";
                $return['result']['status'] = "success";
                $return['result']['message'] = "Not charged for forward";
                $dataarray['message_context'] = "forward";
            //    $dataarray['message_contextId'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['id'];
             //   $dataarray['message_context_rom'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['from'];
                $this->writelog($dataarray, "Save data for new Reply message");
                //    $save_status = $this->_savedata($dataarray, $FBSettings);  // no default reply needed for 
            }else{ //new msg
          //      debug("New msg");
                $return['result']['msg_context'] = "New message received, No need of rating";
                $return['result']['status'] = "success";
                $dataarray['message_context'] = "new";
                $this->writelog($dataarray, "Save New Massage in streams");
                //   $save_status = $this->_savedata($dataarray, $FBSettings); //save data before sending welcome msg.

                if (($Timeout) && ($msgtype != "interactive")&&($isCmd == false)) {  // new message and not reply for interactive msg and commands. 
                    $this->writelog($msgtype, "Sending Interactive Menu to " . $dataarray['contact_waid']);
                    $data = [
                        "mobile_number" => $dataarray['contact_waid'],
                        "schedule_name" => "welcomemsg_grand"
                    ];
                    //  debug($FBSettings);
                    $notification_numbers = (explode(',', $FBSettings['interactive_notification_numbers']));
                    $notification_numbers[] = $dataarray['contact_waid'];
                    $this->writelog($notification_numbers, "Iteractive Menu notification array");
               //     debug($input);
                    foreach ($notification_numbers as $key => $contact_number) {
                        if (!empty($contact_number)) {
                            $this->writelog($contact_number, "Sending Interactive Menu to $contact_number for customer id: " . $dataarray['contact_waid']);
                            if (!empty($FBSettings['interactive_menu_function'])) {
                                $this->readmsg($messageid, $FBSettings); //existing interactive communcatoin. 
                                $interactive_menu_function = $FBSettings['interactive_menu_function'];
                                $this->$interactive_menu_function($dataarray['contact_waid'], $contact_number, $FBSettings);
                            }
                            //can add welcome message function here. 
                        }
                    }
                } else {
                    $this->writelog("Skipping welcome message time out is not yet happen after last message or Intrective message");
                }
            }
            $save_status = $this->_savedata($dataarray, $FBSettings);  // no default reply needed for 

            if(!$isCmd){ //admin forward only if the message is not command. 
                $this->adminforwarder($save_status['id'], $FBSettings,$sender); //passing stream ID and Account id.
            }
           
            // $result = array("success" => true);
            // return $this->response->withType("application/json")->withStringBody(json_encode($result));
        }elseif (isset($input['entry'][0]['changes'][0]['value']['statuses'])) {  //type ie status update.
            //    $status = $input['entry'][0]['changes'][0]['value']['statuses'][0];
            $status = $input['entry'][0]['changes'][0]['value']['statuses'];
            $this->writelog($status, "Picked up by Status update");
            $return['status'] = $status;
        } else {
            $this->writelog($input, "Posted data");
        }

        return $return;
        // sleep(2);
    }


    


    function processCMd($dataarray,$FBSettings){ //this command is for processing and checking the if the message is a command. now, only "register" is added.
        $CMDarray = explode(' ', $dataarray['message_txt_body']);
     //   debug($CMDarray);
        $cmd = strtolower($CMDarray[0]);
        $sendarray=[];
        $CommantTable=$this->getTableLocator()->get('Commands');
        $comand=$CommantTable->find()
        ->where(['cmd'=> $cmd,'account_id'=>$FBSettings['account_id']])
        ->all();
    //    debug($comand);
        if($comand->isEmpty()){
            $this->writelog($cmd, "Command: The provided command is not one of the listd command, false.  ");
            return false;
        }else{
            $this->writelog($cmd, "Command: command is found $cmd ");
                switch( $cmd){
                    case "register":
                        //67265
                    //    echo "Processing pending list in register for ". $dataarray['contact_waid'];
                        $this->writelog($dataarray['contact_waid'], "Command: Processing register");
                        $contact_stream_id= $this->getWastreamsContactId($dataarray['contact_waid'], $FBSettings);  
                        $streamTable=$this->getTableLocator()->get('Streams');
                        $failedQ=$streamTable->find()
                        ->where(['contact_stream_id'=>$contact_stream_id,'type'=>'forward','success'=>0])
                        ->all();
                        if($failedQ->isEmpty()){
                            $this->writelog($failedQ, "Command: Failed Q is empty for  ".$dataarray['contact_waid'] ." with contact ID contact_stream_id");
                        }else{
                           foreach($failedQ as $key =>$val){
                                debug("resending $val->id");
                                $this->writelog($val, "Command: Failed Q senidng with ID $val->id on   ".$dataarray['contact_waid'] ." with contact ID contact_stream_id");
                                $this->resend($val->id);  

                           }
                        }
                       
                        break;
                    default:
                        $this->writelog($cmd, "Command:  nothing code for $cmd");
                    break;
                    
                }       
        }
        return true;
    }


    function sendFailedForwards($phone_number){


    }
 
    


    function adminforwarder($stream_id,$FBSettings, $sender){ //this function will be called from rcv array to formward this all receving message to admins
        //    $this->viewBuilder()->setLayout('ajax');
            //find all users under this account to notify.
      //      debug("Running admin fowader");
            $UserTable=$this->getTableLocator()->get('Users');
            $users=$UserTable->find()
            ->where(['account_id'=>$FBSettings['account_id']])
            ->whereNotNull('mobile_number');
        //    debug($users);
            foreach ($users as $key =>$val){
                
                $this->escortadminmsg($val->mobile_number, $sender,$FBSettings,$sender);

                $sendQData['mobile_number']=$val->mobile_number;
                $sendQData['type']="forward";
                $sendQData['api_key']=$this->getMyAPIKey($FBSettings['account_id']);
                $sendQData['stream_id']=$stream_id;
                $sendQ = $this->getTableLocator()->get('SendQueues');
                $sendQrow = $sendQ->newEmptyEntity();
                $sendQrow->form_data = json_encode($sendQData);
                $sendQrow->status = "queued";
                $sendQrow->type = "forward";
                $result=[];
                if($sendQ->save($sendQrow)){
                    $result['status']="success";
                    $result['msg']="Message queued for delivery, $sendQrow->id";
                }else{
                    $result['status']="failed";
                    $result['msg']="Failed to forward";
                }
                // debug($sendQData);
                // debug($result);
    
            }
           
    }

    function request_welcomeDelete($dataarray, $FBSettings)
    {
        // debug("Sending welcome msg");
        if (isset($FBSettings['welcome_msg'])) {
            //   debug($dataarray['recievearray']);

            $msgArray = json_decode($dataarray['recievearray'], true);
            //      debug($msgArray);
            $sender = $msgArray['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
       //     $sender = "966547237272"; //flood
            $sendQData['mobile_number'] = $sender;
            $sendQData['type'] = "send";
            $sendQData['var-1'] = $FBSettings['welcome_msg'];
            $sendQData['schedule_name'] = $FBSettings['rcv_notification_template'];
            $sendQData['api_key'] = $this->getMyAPIKey($FBSettings['account_id']);
            $sendQ = $this->getTableLocator()->get('SendQueues');
            $sendQrow = $sendQ->newEmptyEntity();
            $sendQrow->form_data = json_encode($sendQData);
            $sendQrow->status = "queued";
            $sendQrow->type = "send";
        //    debug($sendQ);
          //  $result = [];
            if ($sendQ->save($sendQrow)) {
                $result['status'] = "success";
                $result['msg'] = "Escort Message queued for delivery, $sendQrow->id";
            } else {
                $result['status'] = "failed";
                $result['msg'] = "Failed to forward";
            }

            return $result;
        }

    }


    function request_welcome($dataarray, $FBSettings)
    {
        // debug("Sending welcome msg");
        if (isset($FBSettings['welcome_msg'])) {
            $msgArray = json_decode($dataarray['recievearray'], true);
            $sender = $msgArray['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
           // $sender = "966547237272"; //flood
            $sendQData['mobile_number'] = $sender;
            $sendQData['type'] = "send";
            // $sendQData['var-1'] = "wa.me/".$customer_number;
            $sendQData['schedule_name'] = $FBSettings['welcome_msg'];
            $sendQData['api_key'] = $this->getMyAPIKey($FBSettings['account_id']);
            $sendQ = $this->getTableLocator()->get('SendQueues');
            $sendQrow = $sendQ->newEmptyEntity();
            $sendQrow->form_data = json_encode($sendQData);
            $sendQrow->status = "queued";
            $sendQrow->type = "send";
            $result = [];
            if ($sendQ->save($sendQrow)) {
                $result['status'] = "success";
                $result['msg'] = "Escort Message queued for delivery, $sendQrow->id";
            } else {
                $result['status'] = "failed";
                $result['msg'] = "Failed to forward";
            }
            return $result;
        }

    }

    function escortadminmsg($admin_mobile, $customer_number,$FBSettings,$sender){ //this function sedna message,  "You have a message from $sender enve ther
        $sendQData['mobile_number'] = $admin_mobile;
        $sendQData['type'] = "send";
        $sendQData['var-1'] = "wa.me/".$customer_number;
        $sendQData['schedule_name']=$FBSettings['rcv_notification_template'];
        $sendQData['api_key'] = $this->getMyAPIKey($FBSettings['account_id']);
        $sendQ = $this->getTableLocator()->get('SendQueues');
        $sendQrow = $sendQ->newEmptyEntity();
        $sendQrow->form_data = json_encode($sendQData);
        $sendQrow->status = "queued";
        $sendQrow->type = "send";
        $result=[];
        if($sendQ->save($sendQrow)){
            $result['status']="success";
            $result['msg']="Escort Message queued for delivery, $sendQrow->id";
        }else{
            $result['status']="failed";
            $result['msg']="Failed to forward";
        }
        // debug($sendQData);
        // debug($result);
    }

    function _processInteractive($input, $FBSettings)
    {
        $postarray = json_decode($input, true);
        $this->writeinteractive($postarray, "input array ");
        $interactive = $postarray['entry'][0]['changes'][0]['value']['messages'][0]['interactive'];
        $wa_id = $postarray['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
        $repyId = $interactive['list_reply']['id'];
        $this->writeinteractive($repyId, "Reply ID");
        $query_str = parse_url($repyId, PHP_URL_QUERY);
        parse_str($repyId, $get_array);
        $this->writeinteractive($get_array, "Reply ID Array to send to Grand");

        $URL = $FBSettings['interactive_webhook'];
//        debug($URL);
        $APIKEY = $FBSettings['interactive_api_key'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($get_array),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'APIKEY: ' . $APIKEY,
                //  'Cookie: CAKEPHP=rn8u792v5kqp6n3lic5m43ejvc'
            ),
        ));

        $response = curl_exec($curl);
  //              print($response);
        $this->writeinteractive($response, "Response json from Grand");
        $responsearray = json_decode($response, true);
     //      debug($responsearray);
        $this->writeinteractive($responsearray, "Response Array from Grand");
        //  $notification_numbers=$this->_getAccountSettings('interactive_notification_numbers');
        curl_close($curl);
        $notification_numbers = (explode(',', $FBSettings['interactive_notification_numbers']));
        $notification_numbers[] = $wa_id;

      //  return false; //to be removed. 

        //if result has intereactive=false, dont send menu again. 
        if( $responsearray['interactive']==true){
            if (!empty($responsearray)) {
                foreach ($notification_numbers as $key => $contact_number) {
                    $this->_sendIntToCustomer($responsearray, $contact_number, $FBSettings);
                }
            } else {
                $this->writeinteractive($response, "Failed to send response array as its empty");
                $this->writelog($response, "Failed to send response array as its empty");
            }
        }else{
            $this->writeinteractive($response, "No more menu to send. ");
        }
       
    }

    function _sendIntToCustomer($list, $wa_id, $FBSettings)
    {
        $frame = '{
                "to": "' . $wa_id . '",
                "messaging_product": "whatsapp",
                "recipient_type": "individual",
                "type": "interactive",
                "interactive": {
                    "type": "list",
                    "header": {
                        "type": "text",
                        "text": "' . $list['header'] . '"
                    },
                    "body": {
                        "text": "' . $list['body'] . '"
                    },
                    "footer": {
                        "text": "Thank you for reaching Grand electronics and Home Appliances"
                    },
                    "action": {
                        "button": "' . $list['button'] . '",
                        "sections": [
                        ]
                    }
                }
            }';
        $frame = (json_decode($frame, true));

        $frame['interactive']['action']['sections'] = $list['result'];
        $this->writeinteractive($frame, "Frame to send");
        $jsonlist = json_encode($frame, JSON_PRETTY_PRINT);
        $this->writeinteractive($jsonlist, "Json Send array");
        $ACCESSTOKENVALUE = $FBSettings['ACCESSTOKENVALUE'];

        $curl = curl_init();
        //sending menu to customer. 
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/' . $FBSettings['phone_numberId'] . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonlist,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBSettings['ACCESSTOKENVALUE']
            ),
        ));

        $jsonresponse = curl_exec($curl);
        $this->writeinteractive($jsonresponse, "Response from menu send");
        curl_close($curl);
        $response = json_decode($jsonresponse, true);

        $table = $this->getTableLocator()->get('Streams');
        $row = $table->newEmptyEntity();

        $row->contact_stream_id = $this->getWastreamsContactId($wa_id, $FBSettings);
        $row->account_id = $FBSettings['account_id'];
        if (isset($response['messages'][0]['id'])) {
            $row->messageid = $response['messages'][0]['id'];
            $row->type = "ISend";
            $row->has_wa = true;
            $row->success = true;
            $row->result = $jsonresponse;
            $row->sendarray = $jsonlist;
        } else {
            $this->writelog($response, "Response error");
            $row->has_wa = false;
            $row->type = "ISend";
            $row->result = $jsonresponse;
            $row->success = false;
            $row->sendarray = $jsonlist;
        }
        if ($table->save($row)) {
            $this->writeinteractive($row, "updated Stream table");
        } else {
            $this->writeinteractive($row->getErrors(), "Failed to update Stream table");
        }
    }

    function readmsg($MESSAGE_ID, $FBSettings)
    { //Notify FB about message is read. 
        $curl = curl_init();
        $this->writelog($MESSAGE_ID, "Message ID");
        $POSTFIELDS = '{
          "messaging_product": "whatsapp",
          "status": "read",
          "message_id": "' . $MESSAGE_ID . '"
        }';
        $this->writelog($POSTFIELDS, "POSTFIELDS");
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/' . $FBSettings['API_VERSION'] . '/' . $FBSettings['phone_numberId'] . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POSTFIELDS,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBSettings['ACCESSTOKENVALUE']
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $this->writelog($response, "Read Response");
    }

    function _formate_date($ts)
    {
        //  $this->writelog($ts, "coverting");
        if (isset($ts)) {
            $ts = (int) $ts;
            $newdate = (date('Y-m-d H:i:s', $ts));
            $this->writelog($newdate, "new date");
            return $newdate;
        } else {
            return null;
        }
    }

    function _checktimeout($contact = null)
    {
        $this->writelog($contact, "Checking timout for  $contact");
        $mobile = $this->getTableLocator()->get('ContactStreams')->find()->where(['contact_number' => $contact])->toArray();
        if (!empty($mobile)) {
            $CHAT_TIMEOUT = $this->_getsettings('CHAT_TIMEOUT');
            $query = $this->getTableLocator()->get('Streams')->find();
            $query->where([
                'Streams.created >=' => date('Y-m-d H:i:s', strtotime('-' . $CHAT_TIMEOUT . ' seconds')),
                'contact_stream_id' => $mobile[0]['id'],
                // 'Streams.created' => "send"
            ])
                ->first();

            $result = $query->toArray();
            // $this->writelog(sql($query), "Check timeout query on $contact");
            //   debug(sql($query));
            if (empty($result)) {
                // debug("Sending message");
                $this->writelog($contact, "new message will be replied $contact");
                return true;
            } else {
                //   debug($result);
                //  debug("not Sending message");
                $this->writelog($contact, "new message not be replied to $contact");
                return false;
            }
        }
        return true;
    }

    function _savedata($data, $FBSettings)
    {
        # $this->writelog($data, "Data to be saved");
        if (isset($data['contact_waid'])) {
            $data['contact_stream_id'] = $this->getWastreamsContactId($data['contact_waid'], $FBSettings);
        }
        if (isset($data['contacts_profile_name'])) {
            $this->updateProfileWastreamsContact($data['contact_waid'], $data['contacts_profile_name'], $FBSettings);
        }
        $Streams = $this->getTableLocator()->get('Streams');
        $record = $Streams->newEntity($data);
        if ($record->getErrors()) {
            $result['status'] = "failed";
            $result['msg'] = "Validation errors";
            $this->set('result', $result);
            $this->writelog($record->getErrors(), "Error");
        }

        if ($Streams->save($record)) {
            $result['status'] = "success";
            $result['msg'] = "Data has been saved";
            $result['id'] = $record->id;
        } else {
            $result['status'] = "failed";
            $result['msg'] = "Not able to save the streams";
            $this->writelog($record->getErrors(), "Stream save Failed due to below error");
        }

        $this->writelog($result, "Save status");
        return $result;
    }

    function _update_status($return,$FBSetting)
    {
        $statuses = $return['status'];

        $this->writelog($statuses, "Updating status");
        foreach ($statuses as $key => $status) {
            $this->writelog($status, "Updating status $key");
            $query = $this->getTableLocator()->get('Streams')->find();
            $query->where([
                'OR' => ['replyid' => $status['id'], 'messageid' => $status['id']]
            ]);

            $result = $query->toArray();

            //checking any matching with existing 
            if ((!empty($result)) && (isset($result[0]['id']))) {
                $this->writelog($result, "Reply ID match in Streams table");
                $stream_id = $result[0]['id'];
                $this->writelog($result, "is  the result of searching reply ID:" . $status['id']);
                $StreamTable = $this->getTableLocator()->get('Streams');
                $editrow = $StreamTable->get($stream_id);
                //                debug($status);
                $return['result']['statustype'] = $status['status'];
                switch ($status['status']) {
                    case "delivered":
                        $editrow->delivered_time = $this->_formate_date($status['timestamp']);
                        break;
                    case "read":
                        $editrow->read_time = $this->_formate_date($status['timestamp']);
                        break;
                    case "sent":
                        $editrow->sent_time = $this->_formate_date($status['timestamp']);
                        break;
                    case "failed":
                        $editrow->sent_time = $this->_formate_date($status['timestamp']);
                        $editrow->success = 0;
                        $editrow->errors = json_encode($status['errors']);
                        break;
                    default:
                        $this->writelog(($status['status']), "Wrong status");
                        break;
                }
                if (isset($status['pricing'])) {
                    $return['result']['pricing'] = true;
                    $this->writelog($status['pricing'], "UpdatingPricing");
                    $editrow->billable = $status['pricing']['billable'];
                    $editrow->pricing_model = $status['pricing']['pricing_model'];
                    $editrow->category = $status['pricing']['category'];
                } else {
                    $return['result']['pricing'] = false;
                }
                if (isset($status['conversation'])) {
                    $editrow->conversationid = $status['conversation']['id'];
                    if (isset($status['conversation']['expiration_timestamp'])) {
                        $editrow->conversation_expiration_timestamp = $this->_formate_date($status['conversation']['expiration_timestamp']);
                    }
                    $editrow->conversation_origin_type = $status['conversation']['origin']['type'];
                    $return['result']['message'] = "Updated Coversation info.";
                } else {
                    $return['result']['message'] = "No conversation info found in message to process rating";
                }

                //just to append the json in streams table.

                $existing_update = $editrow->tmp_upate_json;
                $editrow->tmp_upate_json = $existing_update . ",\n" . json_encode($status);
                //End of update. 

                if ($StreamTable->save($editrow)) {
                    $this->writelog($editrow, "Save Success");
                    $return['result']['message'] = $return['result']['message'] . "  Data Saved in Streams";
                    $return['result']['status'] = "success";
                } else {
                    $return['result']['message'] = $return['result']['message'] . "  Failed to save in stream";
                    $return['result']['status'] = "failed";
                    $this->writelog($editrow, "Save Failed");
                }



                if (isset($status['conversation'])) {
                    $ratingquery = $this->getTableLocator()->get('Ratings')->find();
                    $ratingquery->where([
                        ['conversation' => $status['conversation']['id']]
                    ]);
                    $ratingResults = $ratingquery->all();
                    //Billing is needed only for Uniq conversation IDS. 
                    if ($ratingResults->isEmpty()) {
                       // $return = $this->_rateMe($status,$FBSetting); //rating.
                        //rating api.

                    //    $ths->ratingApi($qid,$api)

                        //add the rating curl url




                        // $return['result']['rating'] = "new";
                    } else {
                        $return['result']['rating'] = "existing rating " . $status['conversation']['id'];
                        // debug($ratingquery);

                        if (!isset($updated[$status['conversation']['id']])) {
                            // debug("Already Rated updating all fields of " . $status['conversation']['id']);
                            $streamsTable = $this->getTableLocator()->get('Streams');
                            $streamsTable->updateAll(
                                ['rated' => true],
                                ['conversationid' => $status['conversation']['id']]
                            );
                            $updated[$status['conversation']['id']] = true;
                            // debug ("Updated all rated.");
                            //    debug($updated);
                        } else {
                            //   debug("Already updated ");
                        }
                    }
                }
            } else {
                $this->writelog($result, "No matching record in stream table for message or reply id " . $status['id']);
            }
        }

        return $return;
    }

    function ratingApi($qid, $API_KEY){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost/billings/rating',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('qid' => $qid),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Key: '.$API_KEY
            ),
        ));

        $response = curl_exec($curl);
        return $response;

    //    curl_close($curl);
    //    echo $response;
    }

    function _sendInteractiveMenu($customer_number, $contactToSend, $FBSettings)
    {
        //send hte menu to customer Mobile
        //  $ACCESSTOKENVALUE = $this->_getAccountSettings('ACCESSTOKENVALUE');
        $curl = curl_init();

        $jsonlist = '{
                "to": "' . $contactToSend . '",
                "messaging_product": "whatsapp",
                "recipient_type": "individual",
                "type": "interactive",
                "interactive": {
                    "type": "list",
                    "header": {
                        "type": "text",
                        "text": "How can we Help you "
                    },
                    "body": {
                        "text": "Please select service from Menu. Ref:' . $customer_number . '"
                    },
                  
                    "action": {
                        "button": "Main Menu",
                        "sections": [
                            {
                                "title": "Service",
                                "rows": [
                                    {
                                        "id": "mobile=' . $customer_number . '&action=service",
                                        "title": "Grand service",
                                        "description": "Grand service request"
                                    }
                                ]
                            },
                            {
                                "title": "Sales",
                                "rows": [
                                    {
                                       "id": "mobile=' . $customer_number . '&action=sales",
                                        "title": "Grand Sale",
                                        "description": "Sales, Exchange, 0% Installment&Spare parts",
                                    }
                                ]
                            },
                             {
                                "title": "Free Points",
                                "rows": [
                                    {
                                       "id": "mobile=' . $customer_number . '&action=points",
                                        "title": "Grand Free point",
                                        "description": "See your free grand points to purchase from us",
                                    }
                                ]
                            },
                            {
                                "title": "Quotation",
                                "rows": [
                                    {
                                       "id": "mobile=' . $customer_number . '&action=quotation",
                                        "title": "Quotation",
                                        "description": "Get a quation call from sales team",
                                    }
                                ]
                            },
                            {
                                "title": "Download Invoice",
                                "rows": [
                                    {
                                       "id": "mobile=' . $customer_number . '&action=invoice",
                                        "title": "Download Invoice",
                                        "description": "Selet your product to invoince",
                                    }
                                ]
                            },
                            {
                                "title": "Callback/Enquiry",
                                "rows": [
                                    {
                                       "id": "mobile=' . $customer_number . '&action=callback",
                                        "title": "Call back or Enquiry",
                                        "description": "We will call you back for more details",
                                    }
                                ]
                            }
                        ]
                    }
                }
            }';

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/' . $FBSettings['phone_numberId'] . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonlist,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBSettings['ACCESSTOKENVALUE']
            ),
        ));

        $jsonresponse = curl_exec($curl);
        $this->writeinteractive($jsonresponse, "Response from initial menu");
        curl_close($curl);

        $response = json_decode($jsonresponse, true);

        $table = $this->getTableLocator()->get('Streams');
        $row = $table->newEmptyEntity();

        $row->contact_stream_id = $this->getWastreamsContactId($contactToSend, $FBSettings);
        $row->account_id = $FBSettings['account_id'];

        if (isset($response['messages'][0]['id'])) {
            $row->messageid = $response['messages'][0]['id'];
            $row->type = "ISend";
            $row->has_wa = true;
            $row->success = true;
            $row->result = $jsonresponse;
            $row->sendarray = $jsonlist;
        } else {
            $this->writelog($response, "Response error");
            $row->has_wa = false;
            $row->type = "ISend";
            $row->result = $jsonresponse;
            $row->success = false;
            $row->sendarray = $jsonlist;
        }
        if ($table->save($row)) {
            $this->writeinteractive($row, "updated Stream table");
        } else {
            $this->writeinteractive($row->getErrors(), "Failed to update Stream table");
        }
    }


    function _welcomemsg($customer_number, $contactToSend, $FBSettings)
    {
        // $FBSettings = $this->_getFBsettings($data);
        $data['message'] = "Thank you for Contacting us, for support, please call us.";
        if ($FBSettings['status']['code'] !== 200) {
            $result['status'] = "failed";
            $result['msg'] = "Internal system error, Wrong IP info";
        } else {
            //  $contactStream = $this->getTableLocator()->get('ContactStreams')->get($data['mobilenumberId']);
            $streams_table = $this->getTableLocator()->get('Streams');
            $streamrow = $streams_table->newEmptyEntity();
            //            $streamrow->schedule_id = $sched_id;
            //  $streamrow->contact_stream_id = $data['mobilenumberId'];
            $streamrow->contact_stream_id = $this->getWastreamsContactId($contactToSend, $FBSettings);
            $streamrow->initiator = "welcome";
            $streamrow->type = "Welcome";
            $streamrow->postdata = json_encode($data);
            $streamrow->account_id = $FBSettings['account_id'];
            $streams_table->save($streamrow);
            $contact = $streams_table->get($streamrow->id);
            $form = null;
            $result = $this->_despatch_msg($contact, $data, $form, $FBSettings, "text");
            //debug($result);
            if (isset($result['messages'][0]['id'])) {
                $status['status'] = "success";
                $status['msg'] = json_encode($result);
            } else {
                $status['status'] = "failed";
                $status['msg'] = json_encode($result);
            }
            $this->set('result', $status);
        }
    }






    //!Schdule comapgain related code starts from here. it directly hit here. 
    function sendcamp() //this is the direct endpoint to send campagains and schedule it. 
    { //this API need two values, 1. API-key as header and schedule_id. this will send the scdules to all contactacts. 
        //this is auto triggered when new shedules are added from FE. /campaigns/schedules
        //this will add the schedule to sendQ.


        //validate apis

        $return = array();
        $this->viewBuilder()->setLayout('ajax');
        $apiKey = $this->request->getHeaderLine('X-Api-Key');
        $sched_id = $this->request->getData('sched_id'); // Assuming this is a POST request
        $type = "camp";
        //   debug($type);


        $FBSettings = $this->_getFBsettings($data = ['api_key' => $apiKey]); //This FB settings are just make sure, the paswed API key is valid before processing.
        if ($FBSettings['status']['code'] == 404) {
            $this->response = $this->response->withStatus(401); // Unauthorized
            $this->_update_http_code($sched_id, '404', $type);
            $response['error'] = 'Invalid qid APIKEY';
            $this->set('response', $response);
            return;
        }



        if (!is_numeric($sched_id)) {
            $this->response = $this->response->withStatus(400); // Bad Request
            $this->_update_http_code($sched_id, '400', $type);
            http_response_code(400); // Bad Request
            $response['error'] = 'Invalid Schedule ID  ' . $sched_id;
            $this->set('response', $response);
            return;
        }
        $return = $this->queue_message($sched_id); //the main function to Q the message.

        if (isset($return['result']['error'])) {
            // debug($return);
            $this->_update_http_code($sched_id, '403', $type); //forbidden
            $this->response = $this->response->withStatus(403); // forbidden
            $response['success'] = 'Failed';
        } else {
            $this->_update_http_code($sched_id, '200', $type); //forbidden
            $this->response = $this->response->withStatus(403); // forbidden
            $this->set('response', $return);
           // $response['success'] = 'Failed';
        }

        $this->set('response', $return);
    }

    function queue_message($schedule_id) //used by Camp to Q the messages in send Q.
    {
        $sendarray = [];

        $schedTable = $this->getTableLocator()->get('Schedules');
        $schedQuery = $schedTable->find()
            ->where(['Schedules.id' => $schedule_id])
            ->select([
                'Campaigns.template_id',
                'Campaigns.id',
                'Schedules.name',
                'Schedules.campaign_id',
                'Schedules.user_id',
                'Schedules.account_id',
                'Schedules.contact_csv',
                'Campaigns.auto_inject',
                'Campaigns.inject_text',


            ])
            ->innerJoinWith('Campaigns')
            ->first();
           //     debug($schedQuery->account_id);
        if(empty($schedQuery)) {
            print "No data available in Schedules for $schedule_id";
            $return['result']['error']="Not data in Schedules for $schedule_id";
            return $return;
        }

   

        $contact_array = $this->create_contact_array($schedQuery->contact_csv);

        $record = $schedTable->get($schedule_id);
        $record->total_contact = count($contact_array);
        $schedTable->save($record);

 

        $sendarray['api_key'] = $this->getMyAPIKey($schedQuery->account_id);
        $sendarray['schedule_name'] = $schedQuery->name;

        $template_id = $schedQuery->_matchingData['Campaigns']['template_id'];
        $campaign_id = $schedQuery->campaign_id;
        $templatetable = $this->getTableLocator()->get('Templates');


        $CampaignFormstable = $this->getTableLocator()->get('CampaignForms');
        $form = $CampaignFormstable->find()
            ->where(['campaign_id' => $campaign_id])
            ->all();

        foreach ($form as $key => $val) {
          //  debug($val);
         
            $field_name = $val['field_name'];
            $keyarray = explode("-", $field_name);
    //           debug($keyarray);
            if (($keyarray[0] == "file") && ($keyarray[2] == "header")) {  //its an image. 
                if (isset($val['filename'])) {
                    $sendarray['filename'] = $val['filename'];
                }
                $sendarray['media_id'] = $val['fbimageid']; //this was $sendarray['imageid'] before. corrected but not sure about the real imapct.
            }

            if ($keyarray[0] == "var") {  //parmeters injection. 
                $sendarray['var-' . $keyarray[1]] =$val['field_value'];  
            }

            if ($keyarray[0] == "button") {  //parmeters for button variables. 
                $scheduleArray = $schedQuery->toArray();
                if (isset($scheduleArray['_matchingData']['Campaigns']['auto_inject'])) {
                    $autoInject = $scheduleArray['_matchingData']['Campaigns']['auto_inject'];
                    if ($autoInject == true) {
                        $inject_json = $scheduleArray['_matchingData']['Campaigns']['inject_text']; //this data will be modified to repalce real user data in next loop with contact numbers. 
                        
                    //    debug($inject_json);
                    }
                } else {
                    // Handle the case when auto_inject is not set
                    $sendarray['button_var'] = $val['field_value'];
                }
            }
        }

     //   debug($sendarray);
        $totalSchedules=0;
        $return['result']['duplicate']=0;
        foreach ($contact_array as $contact_number_id => $contact_number) {
            $sendarray['mobile_number'] = $contact_number;
            if ($autoInject == true) { //replace the variables in json data with real value. 


                $modifiedinject_json = str_replace('##mobile##', $contact_number, $inject_json); //can repeate the same for repacing all VARs.

                $injectarray = json_decode($modifiedinject_json, true);
                
            }
         //   debug($scheduleArray);
            if ($scheduleArray['_matchingData']['Campaigns']['auto_inject']) { //auto_inject will enabled track by default. 

                $tableCampsTracker = $this->getTableLocator()->get('CampsTrackers');
                //duplicate checking for existing same contact ID and Campaign ID.
                $duplicate = $tableCampsTracker->find()
                    ->where([
                        'campaign_id' =>$schedQuery->_matchingData['Campaigns']['id'],
                        'contact_number_id' => $contact_number_id
                    ])
                    ->first();

                // debug($duplicate);

                if ($duplicate) {
                    //Update blocked count. 
                    $duplicate->duplicate_blocked += 1; // Increment the duplicate_blocked field
                    $tableCampsTracker->save($duplicate); // Save the changes
                    $return['result']['duplicate_list'][]=$contact_number;
                    $return['result']['duplicate']= $return['result']['duplicate']+1;
                }
                
                // updating tracker table. 
                $RowCampsTracker=$tableCampsTracker->newEmptyEntity();
                $RowCampsTracker->contact_number_id=$contact_number_id;
           
                $RowCampsTracker->campaign_id=$schedQuery->_matchingData['Campaigns']['id'];
                
                if(!$tableCampsTracker->save($RowCampsTracker)){
                //    debug($RowCampsTracker->getErrors()); 
                }else{
                    $injectarray['camps_tracker_id']=$RowCampsTracker->id;
                    $injectarray['account_id']=$schedQuery->account_id; //account id must be immutable. Dont accept from inject var of attachment form.
                    $sendarray['button_var'] = base64_encode(json_encode($injectarray));
                    $RowCampsTracker->hashvalue=$this->createSHAHash($sendarray['button_var']); //adding hashvalue to validate when the user submit  the camp.
                    $tableCampsTracker->save($RowCampsTracker);
                }
            }else{
         //       debug("Track not enabled");
            }
        //    debug($injectarray);
            $json = json_encode($sendarray);
         //   debug($sendarray);
            $sendTable = $this->getTableLocator()->get('SendQueues');
            $newsendQ = $sendTable->newEmptyEntity();
            $newsendQ->form_data = $json;
            $newsendQ->type = "camp";
            $newsendQ->status = "queued";

            if($sendTable->save($newsendQ)){
                $totalSchedules++;
            }
        }
        $return['result']['total']= $totalSchedules;
        return  $return;
    }



    function create_contact_array($contact_csv)
    {
        //  debug($contact_csv);
        $contact_array = [];
        $contact_contact_number_table = $this->getTableLocator()->get('ContactsContactNumbers');
        $contact_id = explode(",", $contact_csv);
       // print_r($contact_id);
        foreach ($contact_id as $ckey => $cval) {
            $query = $contact_contact_number_table->find()->innerJoinWith('ContactNumbers');
            $query->where(['contact_id' => $cval])
                ->select([
                    'ContactsContactNumbers.contact_id',
                    'ContactsContactNumbers.contact_number_id',
                    'ContactNumbers.mobile_number',
                    'ContactNumbers.blocked',
                    'ContactNumbers.id'
                ])
                ->toArray();
                
            foreach ($query as $key => $val) {
              //  debug($val);
                $blocked = $val->_matchingData['ContactNumbers']['blocked'];
                if ($blocked == false) {
       //             debug($val);
                    $concatctNumberID= $val->_matchingData['ContactNumbers']['id'];
                    $contactNumber= $val->_matchingData['ContactNumbers']['mobile_number'];
                    $contact_array[$concatctNumberID] = $contactNumber;
                }
            }
        }
      //  debug($contact_array);
        return array_unique($contact_array);
    }


        function clearText($text) {
            // Only process if it's a string
            if (!is_string($text)) {
                return $text;
            }

            // Remove new lines and tabs
            $cleanText = str_replace(array("\r", "\n", "\t"), "", $text);

            // Replace multiple spaces with a single space
            $cleanText = preg_replace('/ +/', ' ', $cleanText);

            return $cleanText;
        }
      

}