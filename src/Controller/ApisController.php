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

/**
 * 
 * 
 * Apis Controller
 *
 * @method \App\Model\Entity\Api[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApisController extends AppController {

    public function isAuthorized($user) {
        return true;
    }

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);

        $allowedActions = ['webhook', 'sendschedule']; // List of allowed actions

        if (in_array($this->request->getParam('action'), $allowedActions)) {
//            $this->FormProtection->setConfig('validate', false);
        }

//        $this->FormProtection->setConfig('unlockedActions', ['sendschedule']);
        $this->Authentication->allowUnauthenticated(['webhook', 'webhook1', 'sendschedule', 'uploadfile', 'sendmsg']);
    }

    function webhookold() {
        //    $ACCESSTOKENVALUE = $this->_getAccountSettings('ACCESSTOKENVALUE');
        //  $this->writelog($ACCESSTOKENVALUE, "Access token");
        //   $API_VERSION = $this->_getAccountSettings('API_VERSION');
        $this->writelog(array(['someone hit me']), "hit");
        $this->viewBuilder()->setLayout('ajax');
        $query = $this->request->getQueryParams();

        if (!empty($query)) {
            $this->writelog($query, "getQueryParams Webhook");
            //  $this->writelog($query, "getQueryParams");
            $hub_mode = $query['hub_mode'];
            $hub_challenge = $query['hub_challenge'];
            $hub_verify_token = $query['hub_verify_token'];
            $this->writelog($this->request->getQueryParams(), "getQueryParams");

            if ($hub_verify_token === 'latheefp') {
                $this->writelog($hub_challenge, "hub challenge");
                # echo $hub_challenge;

                return $this->response->withType("text/plain")->withStringBody(json_encode((int) $hub_challenge));
            }
        }

        $dataarray = [];
        $input = json_decode(file_get_contents('php://input'), true);
        $this->writelog($input, "Post Data");

        $dataarray['hookid'] = $input['entry'][0]['id'];
        $dataarray['messaging_product'] = $input['entry'][0]['changes'][0]['value']['messaging_product'];
        $phone_number_id = $input['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'];
        $FBSettings = $this->_getFBsettings(['phone_number_id' => $phone_number_id]);
        $dataarray['account_id'] = $FBSettings['account_id'];
        $this->writelog($FBSettings, "FB settings");
        $display_phone_number = $input['entry'][0]['changes'][0]['value']['metadata']['display_phone_number'];
        $dataarray['display_phone_number'] = $display_phone_number;
        $dataarray['phonenumberid'] = $phone_number_id;
        if (isset($input['entry'][0]['changes'][0]['value']['messages'])) { //type is message
            $message = $input['entry'][0]['changes'][0]['value']['messages'][0];
            $dataarray['recievearray'] = file_get_contents('php://input');
            $messageid = $message['id'];
            $this->writelog($messageid, "Picked up by message");
            $dataarray['messageid'] = $messageid;
            $dataarray['message_format_type'] = $message['type'];
            if (isset($dataarray['message_context'])) {
                $dataarray['message_context'] = $message_context;
            }
            $msgtype = $dataarray['message_format_type'];

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
                    $this->_processInteractive(file_get_contents('php://input'), $FBSettings);
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
            $newmsg = $this->_checktimeout($dataarray['contact_waid']); //dont move this function from here , it should be 
            if (isset($input['entry'][0]['changes'][0]['value']['messages'][0]['context'])) {  //reply of existing msg
                $dataarray['message_context'] = "reply";
                $dataarray['message_contextId'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['id'];
                $dataarray['message_context_rom'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['from'];
                $this->writelog($dataarray, "Save data for new Reply message");
                $this->_savedata($dataarray, $FBSettings);  // no default reply needed for 
            } else { //new msg
                $dataarray['message_context'] = "new";
                $this->writelog($dataarray, "Save New Massage in streams");
                $this->_savedata($dataarray, $FBSettings); //save data before sending welcome msg.

                if (($newmsg) && ($msgtype != "interactive")) {  // new message and not reply for interactive msg
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
            $result = array("success" => true);
            return $this->response->withType("application/json")->withStringBody(json_encode($result));
        } elseif (isset($input['entry'][0]['changes'][0]['value']['statuses'])) {  //type ie status update.
            //    $status = $input['entry'][0]['changes'][0]['value']['statuses'][0];
            $status = $input['entry'][0]['changes'][0]['value']['statuses'];
            $this->writelog($status, "Picked up by Status update");

            $this->_update_status($status);
            $this->writelog('Status update', "Message Type");
        } else {
            $this->writelog($input, "Posted data");
        }
    }

//    public

    function webhook() {
        //    $ACCESSTOKENVALUE = $this->_getAccountSettings('ACCESSTOKENVALUE');
        //  $this->writelog($ACCESSTOKENVALUE, "Access token");
        //   $API_VERSION = $this->_getAccountSettings('API_VERSION');
        $this->writelog(array(['someone hit me']), "hit");
        $this->viewBuilder()->setLayout('ajax');
        $query = $this->request->getQueryParams();

        if (!empty($query)) {
            $this->writelog($query, "getQueryParams Webhook");
            //  $this->writelog($query, "getQueryParams");
            $hub_mode = $query['hub_mode'];
            $hub_challenge = $query['hub_challenge'];
            $hub_verify_token = $query['hub_verify_token'];
            $this->writelog($this->request->getQueryParams(), "getQueryParams");

            if ($hub_verify_token === 'latheefp') {
                $this->writelog($hub_challenge, "hub challenge");
                # echo $hub_challenge;

                return $this->response
                ->withType("text/plain")
                ->withStatus(200)
                ->withStringBody(json_encode((int) $hub_challenge));
            }else{
                return $this->response
                ->withType("text/plain")
                ->withStatus(400)
                ->withStringBody(json_encode((int) "400"));
            }
        }

        $RCVQTable = $this->getTableLocator()->get('RcvQueues');
        $newRCVRow = $RCVQTable->newEmptyEntity();
        $newRCVRow->json = file_get_contents('php://input');
        if ($RCVQTable->save($newRCVRow)) {
            // Success: Data was saved successfully
            $this->response = $this->response->withStatus(200); // HTTP 200 OK
        } else {
            // Failure: Data save failed
            $this->response = $this->response->withStatus(500); // HTTP 500 Internal Server Error or another appropriate error code
        }
    }

    // function msg_process($stream_id) {
    //     switch ($msgtype) {
    //         case "text":
    //             $dataarray['message_txt_body'] = $message['text']['body'];
    //             break;
    //         case "button":
    //             $dataarray['button_payload'] = $message['button']['payload'];
    //             $dataarray['button_text'] = $message['button']['text'];
    //             break;
    //         case "document":
    //             break;
    //         case "sticker":
    //             break;
    //         case "unknown":
    //             break;
    //         case "contacts":
    //             break;
    //         case "video":
    //             break;
    //         case "image":
    //             break;
    //         case "interactive":
    //             $this->_processInteractive(file_get_contents('php://input'), $FBSettings);
    //             $this->readmsg($messageid, $FBSettings); //existing interactive communcatoin. 
    //             break;
    //     }
    // }

//    function _sendInteractiveMenu($customer_number, $contactToSend, $FBSettings) {
//        //send hte menu to customer Mobile
//        //  $ACCESSTOKENVALUE = $this->_getAccountSettings('ACCESSTOKENVALUE');
//        $curl = curl_init();
//
//        $jsonlist = '{
//                "to": "' . $contactToSend . '",
//                "messaging_product": "whatsapp",
//                "recipient_type": "individual",
//                "type": "interactive",
//                "interactive": {
//                    "type": "list",
//                    "header": {
//                        "type": "text",
//                        "text": "How can we Help you "
//                    },
//                    "body": {
//                        "text": "Please select service from Menu. Ref:' . $customer_number . '"
//                    },
//                  
//                    "action": {
//                        "button": "Main Menu",
//                        "sections": [
//                            {
//                                "title": "Service",
//                                "rows": [
//                                    {
//                                        "id": "mobile=' . $customer_number . '&action=service",
//                                        "title": "Grand service",
//                                        "description": "Grand service request"
//                                    }
//                                ]
//                            },
//                            {
//                                "title": "Sales",
//                                "rows": [
//                                    {
//                                       "id": "mobile=' . $customer_number . '&action=sales",
//                                        "title": "Grand Sale",
//                                        "description": "Sales, Exchange, 0% Installment&Spare parts",
//                                    }
//                                ]
//                            },
//                             {
//                                "title": "Free Points",
//                                "rows": [
//                                    {
//                                       "id": "mobile=' . $customer_number . '&action=points",
//                                        "title": "Grand Free point",
//                                        "description": "See your free grand points to purchase from us",
//                                    }
//                                ]
//                            },
//                            {
//                                "title": "Quotation",
//                                "rows": [
//                                    {
//                                       "id": "mobile=' . $customer_number . '&action=quotation",
//                                        "title": "Quotation",
//                                        "description": "Get a quation call from sales team",
//                                    }
//                                ]
//                            },
//                            {
//                                "title": "Download Invoice",
//                                "rows": [
//                                    {
//                                       "id": "mobile=' . $customer_number . '&action=invoice",
//                                        "title": "Download Invoice",
//                                        "description": "Selet your product to invoince",
//                                    }
//                                ]
//                            },
//                            {
//                                "title": "Callback/Enquiry",
//                                "rows": [
//                                    {
//                                       "id": "mobile=' . $customer_number . '&action=callback",
//                                        "title": "Call back or Enquiry",
//                                        "description": "We will call you back for more details",
//                                    }
//                                ]
//                            }
//                        ]
//                    }
//                }
//            }';
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://graph.facebook.com/v15.0/' . $FBSettings['phone_number_id'] . '/messages',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => $jsonlist,
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
//                'Authorization: Bearer ' . $FBSettings['ACCESSTOKENVALUE']
//            ),
//        ));
//
//        $jsonresponse = curl_exec($curl);
//        $this->writeinteractive($jsonresponse, "Response from initial menu");
//        curl_close($curl);
//
//        $response = json_decode($jsonresponse, true);
//
//        $table = $this->getTableLocator()->get('Streams');
//        $row = $table->newEmptyEntity();
//
//        $row->contact_stream_id = $this->getWastreamsContactId($contactToSend, $FBSettings);
//        $row->account_id = $FBSettings['account_id'];
//
//        if (isset($response['messages'][0]['id'])) {
//            $row->messageid = $response['messages'][0]['id'];
//            $row->type = "ISend";
//            $row->has_wa = true;
//            $row->success = true;
//            $row->result = $jsonresponse;
//            $row->sendarray = $jsonlist;
//        } else {
//            $this->writelog($response, "Response error");
//            $row->has_wa = false;
//            $row->type = "ISend";
//            $row->result = $jsonresponse;
//            $row->success = false;
//            $row->sendarray = $jsonlist;
//        }
//        if ($table->save($row)) {
//            $this->writeinteractive($row, "updated Stream table");
//        } else {
//            $this->writeinteractive($record->getErrors(), "Failed to update Stream table");
//        }
//    }
//    function _checktimeout($contact = null) {
//        $this->writelog($contact, "Checking timout for  $contact");
//        $mobile = $this->getTableLocator()->get('ContactStreams')->find()->where(['contact_number' => $contact])->toArray();
//        if (!empty($mobile)) {
//            $CHAT_TIMEOUT = $this->_getsettings('CHAT_TIMEOUT');
//            $query = $this->getTableLocator()->get('Streams')->find();
//            $query->where([
//                        'Streams.created >=' => date('Y-m-d H:i:s', strtotime('-' . $CHAT_TIMEOUT . ' seconds')),
//                        'contact_stream_id' => $mobile[0]['id'],
//                            // 'Streams.created' => "send"
//                    ])
//                    ->first();
//
//            $result = $query->toArray();
//            // $this->writelog(sql($query), "Check timeout query on $contact");
//            //   debug(sql($query));
//            if (empty($result)) {
//                // debug("Sending message");
//                $this->writelog($contact, "new message will be replied $contact");
//                return true;
//            } else {
//                //   debug($result);
//                //  debug("not Sending message");
//                $this->writelog($contact, "new message not be replied to $contact");
//                return false;
//            }
//        }
//        return true;
//    }
//    function _update_status($statuses) {
//        foreach ($statuses as $key => $status) {
//            $this->writelog($status, "Updating status $key");
//            $query = $this->getTableLocator()->get('Streams')->find();
//            $query->where([
//                'OR' => ['replyid' => $status['id'], 'messageid' => $status['id']]
//            ]);
//            $result = $query->toArray();
//            $this->writelog($result, "Reply ID match in Streams table");
//            $id = $result[0]['id'];
//            $this->writelog($result, "is  the result of searching reply ID:" . $status['id']);
//            $Table = $this->getTableLocator()->get('Streams');
//
//            if (isset($id)) {
//                $editrow = $Table->get($id);
//                switch ($status['status']) {
//                    case "delivered":
//                        $editrow->delivered_time = $this->_formate_date($status['timestamp']);
//                        break;
//                    case "read":
//                        $editrow->read_time = $this->_formate_date($status['timestamp']);
//                        break;
//                    case "sent":
//                        $editrow->sent_time = $this->_formate_date($status['timestamp']);
//                        break;
//                    case "failed":
//                        $editrow->sent_time = $this->_formate_date($status['timestamp']);
//                        $editrow->success = 0;
//                        $editrow->errors = json_encode($status['errors']);
//                        break;
//                    default:
//                        $this->writelog(($status['status']), "Wrong status");
//                        break;
//                }
//                if (isset($status['pricing'])) {
//                    $this->writelog($status['pricing'], "UpdatingPricing");
//                    $editrow->billable = $status['pricing']['billable'];
//                    $editrow->pricing_model = $status['pricing']['pricing_model'];
//                    $editrow->category = $status['pricing']['category'];
//                }
//                if (isset($status['conversation'])) {
//                    $editrow->conversationid = $status['conversation']['id'];
//                    $editrow->conversation_expiration_timestamp = $this->_formate_date($status['conversation']['expiration_timestamp']);
//                    $editrow->conversation_origin_type = $status['conversation']['origin']['type'];
//                }
//
//                $existing_update = $editrow->tmp_upate_json;
//                $editrow->tmp_upate_json = $existing_update . ",\n" . json_encode($status);
//
//                if ($Table->save($editrow)) {
//                    $this->writelog($editrow, "Save Success");
//                } else {
//                    $this->writelog($editrow, "Save Failed");
//                }
//
//                if (isset($status['conversation'])) {
//                    $ratingquery = $this->getTableLocator()->get('Ratings')->find();
//                    $ratingquery->where([
//                                ['conversation' => $status['conversation']['id']]
//                            ])
//                            ->first();
//                    //Billing is needed only for Uniq conversation IDS. 
//                    if ($ratingquery->isEmpty()) {
//                        debug("Rating " . $status['conversation']['id']);
//                        $this->_rateMe($status);
//                    } else {
//                        // debug($ratingquery);
//
//                        if (!isset($updated[$status['conversation']['id']])) {
//                            debug("Already Rated updating all fields of " . $status['conversation']['id']);
//                            $streamsTable = $this->getTableLocator()->get('Streams');
//                            $streamsTable->updateAll(
//                                    ['rated' => true],
//                                    ['conversationid' => $status['conversation']['id']]
//                            );
//                            $updated[$status['conversation']['id']] = true;
//                            // debug ("Updated all rated.");
//                            debug($updated);
//                        } else {
//                            debug("Already updated ");
//                        }
//                    }
//                }
//            } else {
//                $this->writelog($id, "Got null ID for search " . $status['id']);
//            }
//        }
//    }
//    function readmsg($MESSAGE_ID, $FBSettings) { //Notify FB about message is read. 
//        $curl = curl_init();
//        $this->writelog($MESSAGE_ID, "Message ID");
//        $POSTFIELDS = '{
//          "messaging_product": "whatsapp",
//          "status": "read",
//          "message_id": "' . $MESSAGE_ID . '"
//        }';
//        $this->writelog($POSTFIELDS, "POSTFIELDS");
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://graph.facebook.com/' . $FBSettings['API_VERSION'] . '/' . $FBSettings['phone_number_id'] . '/messages',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => $POSTFIELDS,
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
//                'Authorization: Bearer ' . $FBSettings['ACCESSTOKENVALUE']
//            ),
//        ));
//        $response = curl_exec($curl);
//        curl_close($curl);
//        $this->writelog($response, "Read Response");
//    }
//    function _savedata($data = array(), $FBSettings) {
//        # $this->writelog($data, "Data to be saved");
//        if (isset($data['contact_waid'])) {
//            $data['contact_stream_id'] = $this->getWastreamsContactId($data['contact_waid'], $FBSettings);
//        }
//        if (isset($data['contacts_profile_name'])) {
//            $this->updateProfileWastreamsContact($data['contact_waid'], $data['contacts_profile_name'], $data);
//        }
//        $Table = $this->getTableLocator()->get('Streams');
//        $record = $Table->newEntity($data);
//        if ($record->getErrors()) {
//            $result['status'] = "failed";
//            $result['msg'] = "Validation errors";
//            $this->set('result', $result);
//            $this->writelog($record->getErrors(), "Error");
//        }
//
//        if ($Table->save($record)) {
//            $result['status'] = "success";
//            $result['msg'] = "Data has been saved";
//            $result['id'] = $record->id;
//        } else {
//            $result['status'] = "failed";
//            $result['msg'] = "Not able to save the streams";
//            $this->writelog($record->getErrors(), "Stream save Failed due to below error");
//        }
//
//        $this->writelog($result, "Save status");
//        return $result;
//    }

    function _formate_date($ts) {
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

    function _gettemplatedata($template_name = null) {
        $this->viewBuilder()->setLayout('ajax');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/' . $this->_getAccountSettings('API_VERSION') . '/' . $this->_getAccountSettings('WBAID') . '/message_templates?limit=3&access_token=' . $this->_getAccountSettings('ACCESSTOKENVALUE') . '&name=' . $template_name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '{
        "messaging_product": "whatsapp",
        "status": "read",
      }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $template = (json_decode($response, TRUE));
        $tbutton = null;
        $tbody = null;
        $theader = null;
        if (isset($template['data'])) {
            foreach ($template['data'][0]['components'] as $key => $val) {
                switch ($val['type']) {
                    case "HEADER":
                        $theader = "<b>" . $val['text'] . "</b>";
                        break;
                        ;
                    case "BODY":
                        $tbody = $val['text'];
                        break;
                        ;
                    case "BUTTONS":
                        foreach ($val['buttons'] as $bkey => $bval) {
                            $tbutton = $tbutton . "<button>" . $bval['text'] . "</button>";
                        }
                }
            }
        }
        $msg = ($theader . "<br>" . $tbody . "<br>" . $tbutton);
        return $msg;
    }

    function sendscheduleold() {
        // debug("Send Schdule");
        $this->viewBuilder()->setLayout('ajax');
        $this->writelog("Whatsapp Schedule function hit", null);
        $data = $this->request->getData();
        ;
        if ($this->request->is('post')) {

            $sendQ = $this->getTableLocator()->get('SendQueues');
            $sendQrow = $sendQ->newEmptyEntity();
            $sendQrow->form_data = json_encode($data);
            $sendQrow->status = "queued";
            $sendQ->save($sendQrow);
            
            $this->writelog($data, "The is post data");
            $data = $this->_getFBsettings($data);
            if ($data['status']['code'] == 200) {
                $this->writelog($data['status']['code'], "Api Validated");
                $data['user_id'] = null;

                //passing the post data to real send funtion.
                $result = $this->_send_schedule($data);
                $this->set('result', $result);
            } else {

                $this->set('result', $data['status']);
            }
        } else {
            $result['status'] = "failed";
            $result['message'] = "Wrong request type";
            $this->set($result, $result);
            $this->writelog($data, "Not Post data");
        }
    }

    function sendschedule() {
        // debug("Send Schdule");
        $this->viewBuilder()->setLayout('ajax');
        $this->writelog("Whatsapp Schedule function hit", null);
        $data = $this->request->getData();
        ;
        if ($this->request->is('post')) {
            $this->writelog($data, "The is post data");
            $data = $this->_getFBsettings($data);
            if ($data['status']['code'] == 200) {
                $this->writelog($data['status']['code'], "Api Validated");
                $data['user_id'] = null;
                //passing the post data to real send funtion.
                $sendQ = $this->getTableLocator()->get('SendQueues');
                $sendQrow = $sendQ->newEmptyEntity();
                $sendQrow->form_data = json_encode($data);
                $sendQrow->status = "queued";
                $sendQ->save($sendQrow);

                http_response_code(200); // Bad Request
                $response['error'] = 'Message Submitted';
                $this->set('result', $response);
                return;



          //      $result = $this->_send_schedule($data);
            //    $this->set('result', $result);
            } else {
                http_response_code(404); // Bad Request
                $response['error'] = 'Wrong API';
                $this->set('result', $response);
                return;
            }
        } else {
            $this->writelog($data, "Not Post data");
            http_response_code(400); // Bad Request
                $response['error'] = 'Bad  request';
            $this->set('result', $response);
              //  return;
        }

        $this->set('response', $return['result']);
    }

    function _send_schedule($data) {
        //   debug("_Send Schdule");
        $this->writelog($data, "Processing shedule data from _send_scheduel function");
        //checking schdule name.
        //  debug($data);
        //Checking shedule name exists or not. 
        $schedTable = $this->getTableLocator()->get('Schedules');
        $schedQuery = $schedTable->find()
                ->where(['Schedules.name' => $data['schedule_name']])
                ->select(['Campaigns.template_id', 'Schedules.campaign_id', 'id'])
                ->innerJoinWith('Campaigns')
                ->first();
        ;
        if (empty($schedQuery)) {
            $this->writelog($schedQuery, "Shedule query result is empty, no matching schedule name");
            $result['status']['type'] = "Error";
            $result['status']['message'] = "Unknown schedule " . $data['schedule_name'];
            $result['status']['code'] = 500;
            return $result;
        } else {
            $this->writelog($schedQuery, "Found schedule " . $data['schedule_name'] . " in table");
            $sched_id = $schedQuery->id;
            //      debug($schedQuery);
            //!!Do the related updates in console as well. 
            $streams_table = $this->getTableLocator()->get('Streams');
            $streamrow = $streams_table->newEmptyEntity();
            $streamrow->schedule_id = $sched_id;
            $streamrow->contact_stream_id = $this->getWastreamsContactId($data['mobile_number'], $data);
            $streamrow->initiator = "API";
            $streamrow->type = "send";
            $streamrow->postdata = json_encode($data);
            $streamrow->account_id = $data['account_id'];
            $streams_table->save($streamrow);
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
                }
                $formarray[] = $newval;
            }
            //Check for Botton variables:
            if (isset($data['button_var'])) {
                $formarray[] = array('field_value' => $data['button_var'], 'field_name' => 'button-var');
            }



            return $this->_despatch_msg($contact, $formarray, $templateQuery, $data);
        }
    }

    function uploadfile() {
        $this->viewBuilder()->setLayout('ajax');
        $file = $_FILES;
        $data = $this->request->getData();
        $api_status = $this->_validatekey($data);
        $this->writelog($file, "upload file");
        $this->writelog($this->request->getData(), "upload  data");
        $this->writelog($api_status, "Api status");
        if ($api_status['status'] == true) {
            $this->writelog($api_status, "Api Validated");
            $data['user_id'] = $api_status['user_id'];
            if (empty($file)) {
                $this->writelog($file, "Empty file");
                $result['status'] = "error";
                $result['msg'] = "No file mentioned";
            } else {
                $id = $this->_uploadtofb($file);
                $this->writelog($id, "File ID");
                if ($id == 0) {
                    $result['status'] = "failed";
                    $result['msg'] = "file upload filed";
                } else {
                    $result['ID'] = $id;
                }
            }

            $this->set('result', $result);
        } else {
            $this->set($result, $api_status);
        }
        $this->set('result', $result);
    }

    function _uploadtofb($file) { //to be fixed by adding $FBSettings vars. 
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/' . $this->_getAccountSettings('API_VERSION') . '/' . $this->_getAccountSettings('phone_number_id') . '/media',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('file' => new \CURLFILE($file['file']['tmp_name'], $file['file']['type'], 'file'), 'messaging_product' => 'whatsapp'),
            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->_getAccountSettings('ACCESSTOKENVALUE')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $resparray = (json_decode($response, TRUE));
        // debug($resparray);

        if (isset($resparray['id'])) {
            return $resparray['id'];
        } else {
            return 0;
        }
    }

    function _processInteractive($input, $FBSettings) {

        $postarray = json_decode(file_get_contents('php://input'), true);
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
        $this->writeinteractive($response, "Response json from Grand");
        $responsearray = json_decode($response, true);
        $this->writeinteractive($responsearray, "Response Array from Grand");
        //  $notification_numbers=$this->_getAccountSettings('interactive_notification_numbers');
        curl_close($curl);
        $notification_numbers = (explode(',', $FBSettings['interactive_notification_numbers']));
        $notification_numbers[] = $wa_id;
        foreach ($notification_numbers as $key => $contact_number) {
            $this->_sendIntToCustomer($responsearray, $contact_number, $FBSettings);
        }
    }

//    function _sendIntToCustomer($list, $wa_id, $FBSettings) {
//        $frame = '{
//	"to": "' . $wa_id . '",
//	"messaging_product": "whatsapp",
//	"recipient_type": "individual",
//	"type": "interactive",
//	"interactive": {
//		"type": "list",
//		"header": {
//			"type": "text",
//			"text": "' . $list['header'] . '"
//		},
//		"body": {
//			"text": "' . $list['body'] . '"
//		},
//		"footer": {
//			"text": "Thank you for reaching Grand electronics and Home Appliances"
//		},
//		"action": {
//			"button": "' . $list['button'] . '",
//			"sections": [
//			]
//		}
//	}
//}';
//        $frame = (json_decode($frame, true));
//
//        $frame['interactive']['action']['sections'] = $list['result'];
//        $this->writeinteractive($frame, "Frame to send");
//        $jsonlist = json_encode($frame, JSON_PRETTY_PRINT);
//        $this->writeinteractive($jsonlist, "Json Send array");
//        $ACCESSTOKENVALUE = $FBSettings['ACCESSTOKENVALUE'];
//
//        $curl = curl_init();
//        //sending menu to customer. 
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://graph.facebook.com/v15.0/' . $FBSettings['phone_number_id'] . '/messages',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => $jsonlist,
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
//                'Authorization: Bearer ' . $FBSettings['ACCESSTOKENVALUE']
//            ),
//        ));
//
//        $jsonresponse = curl_exec($curl);
//        $this->writeinteractive($jsonresponse, "Response from menu send");
//        curl_close($curl);
//
//        $response = json_decode($jsonresponse, true);
//
//        $table = $this->getTableLocator()->get('Streams');
//        $row = $table->newEmptyEntity();
//
//        $row->contact_stream_id = $this->getWastreamsContactId($wa_id, $FBSettings);
//        $row->account_id = $FBSettings['account_id'];
//        if (isset($response['messages'][0]['id'])) {
//            $row->messageid = $response['messages'][0]['id'];
//            $row->type = "ISend";
//            $row->has_wa = true;
//            $row->success = true;
//            $row->result = $jsonresponse;
//            $row->sendarray = $jsonlist;
//        } else {
//            $this->writelog($response, "Response error");
//            $row->has_wa = false;
//            $row->type = "ISend";
//            $row->result = $jsonresponse;
//            $row->success = false;
//            $row->sendarray = $jsonlist;
//        }
//        if ($table->save($row)) {
//            $this->writeinteractive($row, "updated Stream table");
//        } else {
//            $this->writeinteractive($record->getErrors(), "Failed to update Stream table");
//        }
//    }

    function writeinteractive($data, $type = null) {
        $file = LOGS . 'GrandInt' . '.log';
        #  $data =json_encode($event)."\n";  
        $time = date("Y-m-d H:i:s", time());
        $handle = fopen($file, 'a') or die('Cannot open file:  ' . $file); //implicitly creates file
        fwrite($handle, print_r("\n========================$type : $time============================= \n", true));
        fwrite($handle, print_r($data, true));
        fclose($handle);
    }

    function sendmsg1() { //for school
        $this->viewBuilder()->setLayout('ajax');
        $this->writelog("Whatsapp Schedule function hit", null);
        $this->viewBuilder()->setLayout('ajax');
        $data = $this->request->getData();
        if ($this->request->is('post')) {
            $this->writelog($data, "The is post data");
            //validating API Keys.
            $api_status = $this->_validatekey($data);
            if ($api_status['status'] == true) {
                $this->writelog($api_status, "Api Validated");
                $data['user_id'] = $api_status['user_id'];

                //passing the post data to real send funtion.
                $data['mobile_number'] = "966547237272";
                $result = $this->_send_schedule($data);
                $this->set('result', $result);
            } else {
                $this->set('result', $api_status);
            }
        } else {
            $result['status'] = "failed";
            $result['message'] = "Wrong request type";
            $this->set($result, $result);
            $this->writelog($data, "Not Post data");
        }
    }

    function sendmsg() {
        // debug("Send Schdule");
        $this->viewBuilder()->setLayout('ajax');
        $this->writelog("Whatsapp Schedule function hit", null);
        $data = $this->request->getData();
        ;

        $allowed_numbers = [
            '0966547237272',
            '0966547237272'
        ];

        if (in_array($data['mobile_number'], $allowed_numbers)) {
            if ($this->request->is('post')) {
                $this->writelog($data, "The is post data");
                $data = $this->_getFBsettings($data);
                if ($data['status']['code'] == 200) {
                    $this->writelog($data['status']['code'], "Api Validated");
                    $data['user_id'] = null;

                    //passing the post data to real send funtion.
                    $result = $this->_send_schedule($data);
                    $this->set('result', $result);
                } else {

                    $this->set('result', $data['status']);
                }
            } else {
                $result['status'] = "failed";
                $result['message'] = "Wrong request type";
                $this->set($result, $result);
                $this->writelog($data, "Not Post data");
            }
        } else {
            $result['status'] = "failed";
            $result['message'] = "Demo can be send only to allowed numbers";
            //   debug($result);
            $this->set($result, $result);
        }
    }

    function chargeMe($stream_id) {
        $steam_table = $this->getTableLocator()->get('Streams');
        $streaQuery = $steam_table->find()
                ->where(['id' => $stream_id])
                ->first();
        //   debug($streaQuery);
        //check current stauts of this account.
    }

    function sendchat() {
        $this->viewBuilder()->setLayout('ajax');
        $request = $this->getRequest();
        $data = $this->request->getData();
        //   debug($data);
        $authorizationHeader = $request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s+(.*)/', $authorizationHeader, $matches)) {
            $bearerToken = $matches[1];
        }
        $data['api_key'] = $bearerToken;
        $FBSettings = $this->_getFBsettings($data);
        if ($FBSettings['status']['code'] !== 200) {
            $result['status'] = "failed";
            $result['msg'] = "Internal system error, Wrong IP info";
        } else {
            //  $contactStream = $this->getTableLocator()->get('ContactStreams')->get($data['mobilenumberId']);
            $streams_table = $this->getTableLocator()->get('Streams');
            $streamrow = $streams_table->newEmptyEntity();
//            $streamrow->schedule_id = $sched_id;
            $streamrow->contact_stream_id = $data['mobilenumberId'];
            $streamrow->initiator = "Console";
            $streamrow->type = "Console";
            $streamrow->postdata = json_encode($data);
            $streamrow->account_id = $FBSettings['account_id'];
            $streams_table->save($streamrow);
            $contact = $streams_table->get($streamrow->id);
            $result = $this->_despatch_msg($contact, $data, null, $FBSettings, "text");
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

    function forwarder($id = null) {

        $rcvarray = '{
            "object": "whatsapp_business_account",
            "entry": [
                {
                    "id": "118818951179553",
                    "changes": [
                        {
                            "value": {
                                "messaging_product": "whatsapp",
                                "metadata": {
                                    "display_phone_number": "914952223307",
                                    "phone_number_id": "103908902684969"
                                },
                                "contacts": [
                                    {
                                        "profile": {
                                            "name": "latheef"
                                        },
                                        "wa_id": "966547237272"
                                    }
                                ],
                                "messages": [
                                    {
                                        "from": "966547237272",
                                        "id": "wamid.HBgMOTY2NTQ3MjM3MjcyFQIAEhgUM0FGMUJDOUNFRkU5N0Q5NDdGODUA",
                                        "timestamp": "1695097923",
                                        "type": "image",
                                        "image": {
                                            "caption": "Test",
                                            "mime_type": "image\/jpeg",
                                            "sha256": "tmc9iTQY\/5DBP7fSter0vHkb4X\/admSPEObydgruIyI=",
                                            "id": "1286862582033925"
                                        }
                                    }
                                ] 
                            },
                            "field": "messages"
                        }
                    ]
                }
            ]
        }';

        $msgArray = json_decode($rcvarray, true);

        debug($msgArray);

        $apiTable = $this->getTableLocator()->get('ApiKeys');
        $apiKey = $apiTable->find()
                ->where(['account_id' => $this->getMyAccountID(), 'enabled' => true])
                ->first();

        $data['api_key'] = $apiKey['api_key'];
        debug($data);
        $FBSettings = $this->_getFBsettings($data);
        debug($FBSettings);
        if ($FBSettings['status']['code'] !== 200) {
            $result['status'] = "failed";
            $result['msg'] = "Internal system error, Wrong IP info";
        } else {
            //  $contactStream = $this->getTableLocator()->get('ContactStreams')->get($data['mobilenumberId']);
            $streams_table = $this->getTableLocator()->get('Streams');
            $streamrow = $streams_table->newEmptyEntity();
//            $streamrow->schedule_id = $sched_id;
            $streamrow->contact_stream_id = $data['mobilenumberId']; //
            $streamrow->initiator = "Console";
            $streamrow->type = "Console";
            $streamrow->postdata = json_encode($data);
            $streamrow->account_id = $FBSettings['account_id'];
            $streams_table->save($streamrow);
            $contact = $streams_table->get($streamrow->id);
//            $result = $this->_despatch_msg($contact, $data, null, $FBSettings, "text");
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




// Now, $msgArray contains the JSON data as a PHP array
    }
}
