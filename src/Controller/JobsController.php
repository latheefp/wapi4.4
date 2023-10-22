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
class JobsController extends AppController {

    public function isAuthorized($user) {
        return true;
    }

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);

        $allowedActions = ['runjob']; // List of allowed actions

        if (in_array($this->request->getParam('action'), $allowedActions)) {
//            $this->FormProtection->setConfig('validate', false);
        }
        $this->Authentication->allowUnauthenticated(['runjob']);
    }

//    function runjob() {
//        
//    }

    public function runjob() {
        $this->viewBuilder()->setLayout('ajax');
        $apiKey = $this->request->getHeaderLine('X-Api-Key');
        $FBSettings = $this->_getFBsettings($data = ['api_key' => $apiKey]);
        if ($FBSettings['status']['code'] == 404) {
            $this->response = $this->response->withStatus(401); // Unauthorized
            $this->_update_http_code($qid, '404');
            $response['error'] = 'Invalid qid APIKEY';
            $this->set('response', $response);
            return;
        }

        // Retrieve the 'qid' value from the request
        $qid = $this->request->getData('qid'); // Assuming this is a POST request
        // Validate 'qid' as needed
        // Example: Check if 'qid' is an integer and not empty
        if (!is_numeric($qid) || empty($qid)) {
            $this->response = $this->response->withStatus(400); // Bad Request
            $this->_update_http_code($qid, '400');
            http_response_code(400); // Bad Request
            $response['error'] = 'Invalid qid ' . $qid;
            $this->set('response', $response);
            return;
        }
        $this->processMe($qid);
        $this->_update_http_code($qid, '200');
        $response['success'] = 'Success';
        $this->set('response', $response);;
    }

    function _update_http_code($qid, $code) {
        $table = TableRegistry::getTableLocator()->get('RcvQueues');
        $row = $table->get($qid);
        $row->http_response_code = $code;
        $table->save($row);
    }

    public function processMe($id) {
        // $table = TableRegistry::getTableLocator()->get('RcvQueues');
//        $io->out('proessing ' . $record->id);
        $Qtable = TableRegistry::getTableLocator()->get('RcvQueues');
        $record = $Qtable->get($id);

        // debug(getenv('LOG'));
        $input = json_decode($record->json, true);
        //   debug($input);
        $this->writelog($input, "Post Data");

        $dataarray['hookid'] = $input['entry'][0]['id'];
        $dataarray['messaging_product'] = $input['entry'][0]['changes'][0]['value']['messaging_product'];
        $phone_number_id = $input['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'];
        $FBSettings = $this->_getFBsettings(['phone_number_id' => $phone_number_id]);
        if ($FBSettings['status']['code'] != 200) {
            $record->status = $FBSettings['status']['message'];
            $Qtable->save($record);
            return false;
        }
        //   debug($FBSettings);
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
            $newmsg = $this->_checktimeout($dataarray['contact_waid']); //dont move this function from here , it should be 
            if (isset($input['entry'][0]['changes'][0]['value']['messages'][0]['context'])) {  //reply of existing msg
                $dataarray['message_context'] = "reply";
                $dataarray['message_contextId'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['id'];
                $dataarray['message_context_rom'] = $input['entry'][0]['changes'][0]['value']['messages'][0]['context']['from'];
                $this->writelog($dataarray, "Save data for new Reply message");
                $save_status = $this->_savedata($dataarray, $FBSettings);  // no default reply needed for 
            } else { //new msg
                $dataarray['message_context'] = "new";
                $this->writelog($dataarray, "Save New Massage in streams");
                $save_status = $this->_savedata($dataarray, $FBSettings); //save data before sending welcome msg.

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
            // return $this->response->withType("application/json")->withStringBody(json_encode($result));
        } elseif (isset($input['entry'][0]['changes'][0]['value']['statuses'])) {  //type ie status update.
            //    $status = $input['entry'][0]['changes'][0]['value']['statuses'][0];
            $status = $input['entry'][0]['changes'][0]['value']['statuses'];
            $this->writelog($status, "Picked up by Status update");

            $this->_update_status($status);
            $this->writelog('Status update', "Message Type");
        } else {
            $this->writelog($input, "Posted data");
        }

        // debug($save_status);
//        $Qtable = TableRegistry::getTableLocator()->get('RcvQueues');
//        $row=$Qtable->get($record->id);
        $record->processed = 1;
        $record->status = "processed";
        $Qtable->save($record);
//        debug($row);
        sleep(2);
    }

    function _processInteractive($input, $FBSettings) {
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

    function writeinteractive($data, $type = null) {
        if (intval(getenv('INTERACTIV')) == false) {
            //   debug("No logs");
            return false;
        }
        $file = LOGS . 'GrandInt' . '.log';
        $time = date("Y-m-d H:i:s", time());
        $handle = fopen($file, 'a') or die('Cannot open file:  ' . $file); //implicitly creates file
        fwrite($handle, print_r("\n========================$type : $time============================= \n", true));
        fwrite($handle, print_r($data, true));
        fclose($handle);
    }

    function _sendIntToCustomer($list, $wa_id, $FBSettings) {
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
            $this->writeinteractive($record->getErrors(), "Failed to update Stream table");
        }
    }

    function readmsg($MESSAGE_ID, $FBSettings) { //Notify FB about message is read. 
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

    function _checktimeout($contact = null) {
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

    function _savedata($data = array(), $FBSettings) {
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

    function _update_status($statuses) {
        foreach ($statuses as $key => $status) {
            $this->writelog($status, "Updating status $key");
            $query = $this->getTableLocator()->get('Streams')->find();
            $query->where([
                'OR' => ['replyid' => $status['id'], 'messageid' => $status['id']]
            ]);
            $result = $query->toArray();
            $this->writelog($result, "Reply ID match in Streams table");
            $id = $result[0]['id'];
            $this->writelog($result, "is  the result of searching reply ID:" . $status['id']);
            $Table = $this->getTableLocator()->get('Streams');

            if (isset($id)) {
                $editrow = $Table->get($id);
//                debug($status);
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
                    $this->writelog($status['pricing'], "UpdatingPricing");
                    $editrow->billable = $status['pricing']['billable'];
                    $editrow->pricing_model = $status['pricing']['pricing_model'];
                    $editrow->category = $status['pricing']['category'];
                }
                if (isset($status['conversation'])) {
                    $editrow->conversationid = $status['conversation']['id'];
                    if (isset($status['conversation']['expiration_timestamp'])) {
                        $editrow->conversation_expiration_timestamp = $this->_formate_date($status['conversation']['expiration_timestamp']);
                    }
                    $editrow->conversation_origin_type = $status['conversation']['origin']['type'];
                }

                $existing_update = $editrow->tmp_upate_json;
                $editrow->tmp_upate_json = $existing_update . ",\n" . json_encode($status);

                if ($Table->save($editrow)) {
                    $this->writelog($editrow, "Save Success");
                } else {
                    $this->writelog($editrow, "Save Failed");
                }

                if (isset($status['conversation'])) {
                    $ratingquery = $this->getTableLocator()->get('Ratings')->find();
                    $ratingquery->where([
                        ['conversation' => $status['conversation']['id']]
                    ]);
//                            ->first();
                    $ratingResults = $ratingquery->all();
                    //Billing is needed only for Uniq conversation IDS. 
                    if ($ratingResults->isEmpty()) {
                        debug("Rating " . $status['conversation']['id']);
                        $this->_rateMe($status);
                    } else {
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
                            debug("Already updated ");
                        }
                    }
                }
            } else {
                $this->writelog($id, "Got null ID for search " . $status['id']);
            }
        }
    }

    function _sendInteractiveMenu($customer_number, $contactToSend, $FBSettings) {
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
            $this->writeinteractive($record->getErrors(), "Failed to update Stream table");
        }
    }
}
