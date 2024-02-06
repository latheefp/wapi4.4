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
            $response['error'] = 'Invalid qid ' . $qid;
            $this->set('response', $response);
            return;
        }

        // debug($type);s
        switch ($type) {
            case "send":

                $table = TableRegistry::getTableLocator()->get('SendQueues');
                $sendQrecord = $table->get($qid);
                $form_data = json_decode($sendQrecord->form_data, true);
                $FBSettings = $this->_getFBsettings($data = ['api_key' => $form_data['api_key']]);
                if ($FBSettings['status']['code'] == 404) {
                    $this->response = $this->response->withStatus(401); // Unauthorized
                    $this->_update_http_code($qid, '404', $type);
                    $response['error'] = 'Invalid qid APIKEY';
                    $this->set('response', $response);
                    return;
                }
                if($sendQrecord->type == "forward"){
                    $return = $this->_forwardmsg($sendQrecord, $FBSettings);
                    debug($return);
                }else{
                    $return = $this->_send_schedule($qid, $FBSettings);

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
                    $return = $this->_update_status($return);
                    http_response_code(200); // Good Request
                    $this->_update_http_code($qid, '200', $type);
                    $this->set('response', $return);
                } else {
                    $this->_update_http_code($qid, '200', $type);
                    // $return['result']['status'] = 'Success';
                }

                break;
        }
        //      debug($return);
        $this->set('response', $return['result']);
    }

    function _forwardmsg($sendQrecord, $FBsettings)
    {
        $retun=[];
        $form_data = json_decode($sendQrecord->form_data, true);
     //  debug($form_data);
        
       

        $streamsTable = $this->getTableLocator()->get('Streams');
        if ($streamsTable->exists(['id' => $form_data['stream_id']])) {
            $streams = $streamsTable->get($form_data['stream_id']);
        } else {
            $return['result']['error'] = "Invalid mobile stream id.";
            return $return;
        }
      //  debug($streams);

        switch ($streams->type) {
            case "send":
           //     debug("Send");
                $msgArray = json_decode($streams->sendarray, true);
                break;
            case "receive":
                debug("Recieve");
                $msgArray = json_decode($streams->recievearray, true);
                $message = $msgArray['entry'][0]['changes'][0]['value']['messages'][0];
                $sendarrayJson = '{
                    "to": "966547237272",
                    "messaging_product": "whatsapp",
                    "recipient_type": "individual"
                }';
                $sendarray = json_decode($sendarrayJson, true);
            //    debug($message);
                $type = $message['type'];
                $sendarray['type'] = $type;
                $sendarray['mobile_number'] = $form_data['mobile_number'];
                $payload = [];
                debug($sendarray);
                debug($type);
                debug($message);
                debug($message[$type]['id']);
                switch ($type) {
                    case "image":
                        $payload['id'] = $message[$type]['id'];
                        break;
                    case "document":
                        $payload['id'] = $message[$type]['id'];
                        break;
                    case "video":
                        $payload['id'] = $message[$type]['id'];
                        break;
                    case "text":
                        $payload['body'] = $message[$type]['body'];
                        break;
                    case "location":
                        $payload = $message[$type];
                        break;
                    case "sticker":
                        $payload = $message[$type];
                        break;
                    case "interactive":
                        $payload = $message[$type];
                        break;
                    case "audio":
                        debug("This is an audio");
                        $payload['id'] = $message[$type]['id'];
                        debug($payload);
                    case "reaction":
                        $payload = $message[$type];
                        break;
                    case "contacts":
                        $payload = $message[$type];
                        break;
                }
                $sendarray[$type] = $payload;
                debug($sendarray);
                break;
        }

      //  debug($FBsettings);
        $streams_table = $this->getTableLocator()->get('Streams');
        $streamrow = $streams_table->newEmptyEntity();
     //   $streamrow->schedule_id = $sched_id;
        $streamrow->contact_stream_id = $this->getWastreamsContactId( $form_data['mobile_number'], $FBsettings);
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

        debug($streamrow->id);
        $contact = $streams_table->get($streamrow->id);
        $templateQuery=[];
        $return['result'] = $this->_despatch_msg($contact, $sendarray, $templateQuery, $FBsettings,$type="forward");
        debug($return);
        return $return;
        
       
    }

    function forwardrcv(){

    }

    

    function _update_http_code($qid, $code, $type)
    {

        switch ($type) {
            case 'send':
                $table = TableRegistry::getTableLocator()->get('SendQueues');
                $row = $table->get($qid);
                $row->http_response_code = $code;
                $row->processed = 1;
                $row->status = "processed";
                $table->save($row);
                break;
            case "receive":
                $table = TableRegistry::getTableLocator()->get('RcvQueues');
                $row = $table->get($qid);
                $row->http_response_code = $code;
                $row->processed = 1;
                $row->status = "processed";
                $table->save($row);
                break;
            case "camp":
                $table = TableRegistry::getTableLocator()->get('Schedules');
                $row = $table->get($qid);
                $row->http_response_code = $code;
                //  $row->processed = 1;
                //   $row->status = "processed";
                $table->save($row);
        }
    }




//send job start here.
    function _send_schedule($qid, $FBSettings)
    {
        //   debug("_Send Schdule");
        // debug($FBSettings);
        $return = array();
        $Qtable = TableRegistry::getTableLocator()->get('SendQueues');
        $record = $Qtable->get($qid);

        // $this->viewBuilder()->setLayout('ajax');
        $this->writelog("Whatsapp Schedule function hit", null);
        $data = json_decode($record->form_data, true);
        if (!isset($data['mobile_number'])) {
            $return['result']['error'] = "No mobile number provided";
            return $return;
        }
        if (strlen($data['mobile_number']) >= 10 && is_numeric($data['mobile_number'])) {
            // echo "Valid mobile number!";
        } else {
            $return['result']['error'] = "Invalid mobile number";
            return $return;
        }



        $this->writelog($data, "Processing shedule data from _send_scheduel function");
        $schedTable = $this->getTableLocator()->get('Schedules');
        $schedQuery = $schedTable->find()
            ->where(['Schedules.name' => $data['schedule_name']])
            ->select(['Campaigns.template_id', 'Schedules.campaign_id', 'id'])
            ->innerJoinWith('Campaigns')
            ->first();;
        if (empty($schedQuery)) {
            $return['result']['error'] = "No matching schedule found, " . $data['schedule_name'];
            $this->writelog($schedQuery, "Shedule query result is empty, no matching schedule name");
            return $return;
        } else {
            $this->writelog($schedQuery, "Found schedule " . $data['schedule_name'] . " in table");
            $sched_id = $schedQuery->id;
            //    debug($schedQuery);
            //!!Do the related updates in console as well. 
            $streams_table = $this->getTableLocator()->get('Streams');
            $streamrow = $streams_table->newEmptyEntity();
            $streamrow->schedule_id = $sched_id;
            $streamrow->contact_stream_id = $this->getWastreamsContactId($data['mobile_number'], $FBSettings);
            $streamrow->initiator = "API";
            $streamrow->type = "send";
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

            foreach ($CampaignForm as $key => $val) {
                $newval = array();
                $vararray = explode('-', $val['field_name']);
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
                            $newval['field_value'] = $data[$newvar];
                        }
                        break;
                    case "button":
                        $newvar = "button-var";
                        if (isset($data[$newvar])) {
                            $newval['field_value'] = $data[$newvar];
                        }
                        break;
                }
                $formarray[] = $newval;
            }
            $return['result'] = $this->_despatch_msg($contact, $formarray, $templateQuery, $FBSettings);
            return $return;
        }
    }




    public function processrcv($id)
    {
        // $this->viewBuilder->setLayout('ajax');
        $return['result'] = [];
        // $table = TableRegistry::getTableLocator()->get('RcvQueues');
        //  $io->out('proessing ' . $record->id);
        $Qtable = TableRegistry::getTableLocator()->get('RcvQueues');
        $record = $Qtable->get($id);

        // debug(getenv('LOG'));
        $input = json_decode($record->json, true);
        //  debug($input);

        $this->writelog($input, "Post Data from Process Job");


        $dataarray['hookid'] = $input['entry'][0]['id'];
        $dataarray['messaging_product'] = $input['entry'][0]['changes'][0]['value']['messaging_product'];
        $phone_number_id = $input['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'];
        $FBSettings = $this->_getFBsettings(['phone_number_id' => $phone_number_id]);
        if ($FBSettings['status']['code'] != 200) {
            $record->status = $FBSettings['status']['message'];
            $Qtable->save($record);

            $return['result']['status'] = "failed";
            $return['result']['message'] = "No account related to phone_number_id $phone_number_id";
            return $return;
        }
        //   debug($FBSettings);
        $dataarray['account_id'] = $FBSettings['account_id'];
        $this->writelog($FBSettings, "FB settings");
        $display_phone_number = $input['entry'][0]['changes'][0]['value']['metadata']['display_phone_number'];
        $dataarray['display_phone_number'] = $display_phone_number;
        $dataarray['phonenumberid'] = $phone_number_id;
        if (isset($input['entry'][0]['changes'][0]['value']['messages'])) { //type is message

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

            switch ($msgtype) {
                case "text":
                    $dataarray['message_txt_body'] = $message['text']['body'];
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
                case "interactive":
                    $this->_processInteractive($record->json, $FBSettings);
                    $this->readmsg($messageid, $FBSettings); //existing interactive communcatoin. 
                    break;
            }
            $dataarray['delivered_time'] = date("Y-m-d h:i:s", time());
            $dataarray['type'] = "receive";
            $this->writelog($message, "message");
            $sender = $input['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
            $this->writelog($sender, "is the sender");
            $dataarray['message_timestamp'] = $this->_formate_date($input['entry'][0]['changes'][0]['value']['messages'][0]['timestamp']);
            $dataarray['contacts_profile_name'] = $input['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'];
            $dataarray['contact_waid'] = $sender;
            $Timeout = $this->_checktimeout($dataarray['contact_waid']); //dont move this function from here , it should be 
            if (isset($input['entry'][0]['changes'][0]['value']['messages'][0]['context'])) {  //reply of existing msg
                $return['result']['msg_context'] = "reply";
                $return['result']['status'] = "success";
                $return['result']['message'] = "Not charged for reply";
                $dataarray['message_context'] = "reply";
                $dataarray['message_contextId'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['id'];
                $dataarray['message_context_rom'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['from'];
                $this->writelog($dataarray, "Save data for new Reply message");
                //    $save_status = $this->_savedata($dataarray, $FBSettings);  // no default reply needed for 
            } else { //new msg
                $return['result']['msg_context'] = "New message received, No need of rating";
                $return['result']['status'] = "success";
                $dataarray['message_context'] = "new";
                $this->writelog($dataarray, "Save New Massage in streams");
                //   $save_status = $this->_savedata($dataarray, $FBSettings); //save data before sending welcome msg.

                if (($Timeout) && ($msgtype != "interactive")) {  // new message and not reply for interactive msg
                    $this->writelog($msgtype, "Sending Interactive Menu to " . $dataarray['contact_waid']);
                    $data = [
                        "mobile_number" => $dataarray['contact_waid'],
                        "schedule_name" => "welcomemsg_grand"
                    ];
                    //  debug($FBSettings);
                    $notification_numbers = (explode(',', $FBSettings['interactive_notification_numbers']));
                    $notification_numbers[] = $dataarray['contact_waid'];
                    $this->writelog($notification_numbers, "Iteractive Menu notification array");
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

            // $result = array("success" => true);
            // return $this->response->withType("application/json")->withStringBody(json_encode($result));
        } elseif (isset($input['entry'][0]['changes'][0]['value']['statuses'])) {  //type ie status update.
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
        //        print($response);
        $this->writeinteractive($response, "Response json from Grand");
        $responsearray = json_decode($response, true);
        //   debug($responsearray);
        $this->writeinteractive($responsearray, "Response Array from Grand");
        //  $notification_numbers=$this->_getAccountSettings('interactive_notification_numbers');
        curl_close($curl);
        $notification_numbers = (explode(',', $FBSettings['interactive_notification_numbers']));
        $notification_numbers[] = $wa_id;
        if (!empty($responsearray)) {
            foreach ($notification_numbers as $key => $contact_number) {
                $this->_sendIntToCustomer($responsearray, $contact_number, $FBSettings);
            }
        } else {
            $this->writeinteractive($response, "Failed to send response array as its empty");
            $this->writelog($response, "Failed to send response array as its empty");
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
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/' . $FBSettings['phone_number_id'] . '/messages',
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
            CURLOPT_URL => 'https://graph.facebook.com/' . $FBSettings['API_VERSION'] . '/' . $FBSettings['phone_number_id'] . '/messages',
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
        $Table = $this->getTableLocator()->get('Streams');
        $record = $Table->newEntity($data);
        if ($record->getErrors()) {
            $result['status'] = "failed";
            $result['msg'] = "Validation errors";
            $this->set('result', $result);
            $this->writelog($record->getErrors(), "Error");
        }

        if ($Table->save($record)) {
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

    function _update_status($return)
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
                $id = $result[0]['id'];
                $this->writelog($result, "is  the result of searching reply ID:" . $status['id']);
                $Table = $this->getTableLocator()->get('Streams');
                $editrow = $Table->get($id);
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

                if ($Table->save($editrow)) {
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
                        $return = $this->_rateMe($status);
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
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/' . $FBSettings['phone_number_id'] . '/messages',
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






    //!Schdule comapgain related code starts from here. 
    function sendcamp()
    {


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
        $return = $this->queue_message($sched_id);

        if (isset($return['result']['error'])) {
            // debug($return);
            $this->_update_http_code($qid, '403', $type); //forbidden
            $this->response = $this->response->withStatus(403); // forbidden
            $response['success'] = 'Failed';
        } else {
            $return['message'] = "The message has been scheduled";
        }

        $this->set('response', $return);
    }

    function queue_message($schedule_id)
    {
        $sendarray = [];

        $schedTable = $this->getTableLocator()->get('Schedules');
        $schedQuery = $schedTable->find()
            ->where(['Schedules.id' => $schedule_id])
            ->select(['Campaigns.template_id', 'Schedules.name', 'Schedules.campaign_id', 'Schedules.user_id', 'Schedules.account_id', 'Schedules.contact_csv'])
            ->innerJoinWith('Campaigns')
            ->first();
        //    debug($schedQuery);      
        if (empty($schedQuery)) {
            print "Empty Schedule info\n";
            return false;
        }

        //   debug($schedQuery);


        $contact_array = $this->create_contact_array($schedQuery->contact_csv);

        $record = $schedTable->get($schedule_id);
        $record->total_contact = count($contact_array);
        $schedTable->save($record);

        //debug($schedQuery->account_id);

        $sendarray['api_key'] = $this->getMyAPIKey($schedQuery->account_id);
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
            $sendarray['mobile_number'] = $contact_number;
            //  debug($sendarray);
            $json = json_encode($sendarray);
            $sendTable = $this->getTableLocator()->get('SendQueues');
            $newsendQ = $sendTable->newEmptyEntity();
            $newsendQ->form_data = $json;
            $newsendQ->type = "camp";
            $newsendQ->status = "queued";
            $sendTable->save($newsendQ);
        }
    }



    function create_contact_array($contact_csv)
    {
        //  debug($contact_csv);
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
}
