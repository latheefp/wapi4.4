<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionManager;
// In your Controller or Table class
use Cake\ORM\TableRegistry;
//use Cake\I18n\Time;
use Cake\I18n\FrozenTime; // Import FrozenTime
//use Cake\Datasource\ConnectionManager as CakeConnectionManager;
use App\Controller\AppController; //(path to your controller).
use Cake\Cache\Cache;

//use Cake\I18n\Time;

class ProcessrcvqCommand extends Command {

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
        $parser = parent::buildOptionParser($parser);

        $parser->addOptions(
                [
                    'queue_id' => [
                        'short' => 'i',
                        'help' => 'The queue_id',
                        'required' => false,
                    ],
            ]
        );

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        while (true) {
            if (intval(getenv('RCVQRUN')) == true) {
                print("RCVQRUN enabled,  processing");
                $this->process_rcvq();
                
            }else{
                print("RCVQRUN is disabled,  waiting 300 seconds");
                sleep (300);
            }
        }
       
        
    }

    public function initialize(): void {
        parent::initialize();
        $this->app = new AppController();
    }

    function process_rcvq() {
        $apiKey = 'sm4UFJUHdHi8HXlrqQx2uqUbek4w6ZdlcGmS0enGTFI0pAbIV6EFk6QwtghSOlRh';
        $table = TableRegistry::getTableLocator()->get('RcvQueues');
        while (true) {
         //   print ".";
            $queued = $query = $table->find()
                    ->where([
                        'status' => 'queued',
                    ])
                    ->all();

            foreach ($queued as $key => $val) {
                debug($val->id);
                $lockTimeout = 3; // Example: 2 seconds
                $connection = ConnectionManager::get('default');
                $limit = $this->checklimit();
                while ($limit == false) {
                    //  debug("Limit is False, Sleeping");
                    //   print ".";
                    sleep(2);
                    $limit = $this->checklimit();
                }

                try {
                    // Attempt to begin a transaction with a lock timeout
                    $connection->begin(['timeout' => $lockTimeout]);
//                $currentTimestamp = Time::now();
                    $mysqlFormattedTimestamp = date('Y-m-d H:i:s');
                    $stmt = $connection->execute('UPDATE rcv_queues SET status = ? , process_start_time= ?  WHERE id = ? AND status = ?', ["processing", $mysqlFormattedTimestamp, $val->id, 'queued']);
                    // debug($stmt);
                    $affectedRows = $stmt->rowCount();
                    if ($connection->commit()) {
                        if ($affectedRows > 0) {
                            debug("Transaction committed successfully. {$affectedRows} rows were affected.");
                            $maxParallelProcesses = $this->app->_getsettings('max_parallel_que_processing');
                            $cmd = ROOT . '/bin/runrcvprocess.pl  -i ' . $val->id . ' -k ' . $apiKey . ' >' . ROOT . '/logs/process.log 2>&1 &';
                            debug($cmd);
                            usleep(100);
                            exec($cmd);
                        } else {
                            debug("Transaction committed, but no rows were affected.");
                            continue;
                        }
                    } else {
                        debug("Transaction failed to commit. Database changes not applied.");
                        continue;
                    }
                } catch (\PDOException $e) {
                    $connection->rollback();
                    echo "Database operation failed: " . $e->getMessage();
                    debug("failed");
                }
            }
        }
    }

    function checklimit() {
        $maxParallelProcesses = $this->app->_getsettings('max_parallel_que_processing');
        $table = TableRegistry::getTableLocator()->get('RcvQueues');
        #    date('Y-m-d H:i:s');
        $query = TableRegistry::getTableLocator()->get('RcvQueues')
                ->find();
        // Add the WHERE clause
        $recent_count = $query->where([
                    'STATUS' => 'processing',
                    'process_start_time >' => FrozenTime::now()->subMinutes(5),
                ])
                ->count();

        print".$recent_count.";
        if ($recent_count <= $maxParallelProcesses) {
            //   debug("Current processing count is $recent_count and max is $maxParallelProcesses TRUE");
            //    print " $recent_count ";

            return true;
        } else {
            //   debug("Current processing count is $recent_count and max is $maxParallelProcesses FALSE");

            return false;
        }
    }

//    function _refreshPID($pids = []) {
//        foreach ($pids as $key => $pid) {
//            //  debug($pid);
//            if (posix_kill(intval($pid), 0)) {
//                
//            } else {
//                debug("the process with  $pid is Not runing");
//                unset($pids[array_search($pid, $pids)]);
//            }
//        }
//        return $pids;
//    }

//    function processMe($qidsjson) {
//        debug($qids);
//        $qids = json_decode($qidsjson, true);
//        debug($qids);
//        debug(date('Y-m-d H:i:s') . " processing QIDs");
//        debug($qids);
//        $baseURL = 'http://localhost/jobs/runjob';
//        $apiKey = 'sm4UFJUHdHi8HXlrqQx2uqUbek4w6ZdlcGmS0enGTFI0pAbIV6EFk6QwtghSOlRh';
//        foreach ($qids as $key => $qid) {
//            $url = $baseURL . '?qid=' . $qid;
//            ${"ch" . $qid} = curl_init();
//            curl_setopt(${"ch" . $qid}, CURLOPT_URL, $url);
//            curl_setopt(${"ch" . $qid}, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt(${"ch" . $qid}, CURLOPT_ENCODING, '');
//            $postData = array('qid' => $qid);
//            curl_setopt(${"ch" . $qid}, CURLOPT_POST, 1);
//            curl_setopt(${"ch" . $qid}, CURLOPT_POSTFIELDS, http_build_query($postData));
//            curl_setopt(${"ch" . $qid}, CURLOPT_HTTPHEADER, array(
//                'X-Api-Key: sm4UFJUHdHi8HXlrqQx2uqUbek4w6ZdlcGmS0enGTFI0pAbIV6EFk6QwtghSOlRh'
//            ));
//        }
//
//        debug(date('Y-m-d H:i:s') . " CURL Array created");
//
////create the multiple cURL handle
//        $mh = curl_multi_init();
//        foreach ($qids as $key => $qid) {
//            curl_multi_add_handle($mh, ${"ch" . $qid});
//        }
//
//        debug(date('Y-m-d H:i:s') . " Multi URL Handle Done");
//
//        do {
////            debug ("Running ".${"ch" . $qid});
//            $status = curl_multi_exec($mh, $active);
//            //     debug($status);
//            if ($active) {
//                // Wait a short time for more activity
//                curl_multi_select($mh);
//            }
//        } while ($active && $status == CURLM_OK);
//
//        debug(date('Y-m-d H:i:s') . " CURL Executed");
//
//        foreach ($qids as $key => $qid) {
//            curl_multi_remove_handle($mh, ${"ch" . $qid});
//        }
////        debug(date('Y-m-d H:i:s') . " Connection removed");
//        curl_multi_close($mh);
////        sleep ();
////        debug(date('Y-m-d H:i:s') . " Process submitted");
//    }

//    function _processInteractive($input, $FBSettings) {
//        $postarray = json_decode($input, true);
//        $this->writeinteractive($postarray, "input array ");
//        $interactive = $postarray['entry'][0]['changes'][0]['value']['messages'][0]['interactive'];
//        $wa_id = $postarray['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
//        $repyId = $interactive['list_reply']['id'];
//        $this->writeinteractive($repyId, "Reply ID");
//        $query_str = parse_url($repyId, PHP_URL_QUERY);
//        parse_str($repyId, $get_array);
//        $this->writeinteractive($get_array, "Reply ID Array to send to Grand");
//
//        $URL = $FBSettings['interactive_webhook'];
//        $APIKEY = $FBSettings['interactive_api_key'];
//
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => $URL,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => json_encode($get_array),
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
//                'APIKEY: ' . $APIKEY,
//            //  'Cookie: CAKEPHP=rn8u792v5kqp6n3lic5m43ejvc'
//            ),
//        ));
//
//        $response = curl_exec($curl);
//        $this->writeinteractive($response, "Response json from Grand");
//        $responsearray = json_decode($response, true);
//        $this->writeinteractive($responsearray, "Response Array from Grand");
//        //  $notification_numbers=$this->_getAccountSettings('interactive_notification_numbers');
//        curl_close($curl);
//        $notification_numbers = (explode(',', $FBSettings['interactive_notification_numbers']));
//        $notification_numbers[] = $wa_id;
//        foreach ($notification_numbers as $key => $contact_number) {
//            $this->_sendIntToCustomer($responsearray, $contact_number, $FBSettings);
//        }
//    }
//
//    function writeinteractive($data, $type = null) {
//        if (intval(getenv('INTERACTIV')) == false) {
//            //   debug("No logs");
//            return false;
//        }
//        $file = LOGS . 'GrandInt' . '.log';
//        $time = date("Y-m-d H:i:s", time());
//        $handle = fopen($file, 'a') or die('Cannot open file:  ' . $file); //implicitly creates file
//        fwrite($handle, print_r("\n========================$type : $time============================= \n", true));
//        fwrite($handle, print_r($data, true));
//        fclose($handle);
//    }
//
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
//        $response = json_decode($jsonresponse, true);
//
//        $table = $this->getTableLocator()->get('Streams');
//        $row = $table->newEmptyEntity();
//
//        $row->contact_stream_id = $this->app->getWastreamsContactId($wa_id, $FBSettings);
//        $row->account_id = $FBSettings['account_id'];
//        if (isset($response['messages'][0]['id'])) {
//            $row->messageid = $response['messages'][0]['id'];
//            $row->type = "ISend";
//            $row->has_wa = true;
//            $row->success = true;
//            $row->result = $jsonresponse;
//            $row->sendarray = $jsonlist;
//        } else {
//            $this->app->writelog($response, "Response error");
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
//
//    function readmsg($MESSAGE_ID, $FBSettings) { //Notify FB about message is read. 
//        $curl = curl_init();
//        $this->app->writelog($MESSAGE_ID, "Message ID");
//        $POSTFIELDS = '{
//          "messaging_product": "whatsapp",
//          "status": "read",
//          "message_id": "' . $MESSAGE_ID . '"
//        }';
//        $this->app->writelog($POSTFIELDS, "POSTFIELDS");
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
//        $this->app->writelog($response, "Read Response");
//    }
//
//    function _formate_date($ts) {
//        //  $this->app->writelog($ts, "coverting");
//        if (isset($ts)) {
//            $ts = (int) $ts;
//            $newdate = (date('Y-m-d H:i:s', $ts));
//            $this->app->writelog($newdate, "new date");
//            return $newdate;
//        } else {
//            return null;
//        }
//    }
//
//    function _checktimeout($contact = null) {
//        $this->app->writelog($contact, "Checking timout for  $contact");
//        $mobile = $this->getTableLocator()->get('ContactStreams')->find()->where(['contact_number' => $contact])->toArray();
//        if (!empty($mobile)) {
//            $CHAT_TIMEOUT = $this->app->_getsettings('CHAT_TIMEOUT');
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
//                $this->app->writelog($contact, "new message will be replied $contact");
//                return true;
//            } else {
//                //   debug($result);
//                //  debug("not Sending message");
//                $this->app->writelog($contact, "new message not be replied to $contact");
//                return false;
//            }
//        }
//        return true;
//    }
//
//    function _savedata($data = array(), $FBSettings) {
//        # $this->writelog($data, "Data to be saved");
//        if (isset($data['contact_waid'])) {
//            $data['contact_stream_id'] = $this->app->getWastreamsContactId($data['contact_waid'], $FBSettings);
//        }
//        if (isset($data['contacts_profile_name'])) {
//            $this->app->updateProfileWastreamsContact($data['contact_waid'], $data['contacts_profile_name'], $FBSettings);
//        }
//        $Table = $this->getTableLocator()->get('Streams');
//        $record = $Table->newEntity($data);
//        if ($record->getErrors()) {
//            $result['status'] = "failed";
//            $result['msg'] = "Validation errors";
//            $this->set('result', $result);
//            $this->app->writelog($record->getErrors(), "Error");
//        }
//
//        if ($Table->save($record)) {
//            $result['status'] = "success";
//            $result['msg'] = "Data has been saved";
//            $result['id'] = $record->id;
//        } else {
//            $result['status'] = "failed";
//            $result['msg'] = "Not able to save the streams";
//            $this->app->writelog($record->getErrors(), "Stream save Failed due to below error");
//        }
//
//        $this->app->writelog($result, "Save status");
//        return $result;
//    }
//
//    function _update_status($statuses) {
//        foreach ($statuses as $key => $status) {
//            $this->app->writelog($status, "Updating status $key");
//            $query = $this->getTableLocator()->get('Streams')->find();
//            $query->where([
//                'OR' => ['replyid' => $status['id'], 'messageid' => $status['id']]
//            ]);
//            $result = $query->toArray();
//            $this->app->writelog($result, "Reply ID match in Streams table");
//            $id = $result[0]['id'];
//            $this->app->writelog($result, "is  the result of searching reply ID:" . $status['id']);
//            $Table = $this->getTableLocator()->get('Streams');
//
//            if (isset($id)) {
//                $editrow = $Table->get($id);
////                debug($status);
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
//                        $this->app->writelog(($status['status']), "Wrong status");
//                        break;
//                }
//                if (isset($status['pricing'])) {
//                    $this->app->writelog($status['pricing'], "UpdatingPricing");
//                    $editrow->billable = $status['pricing']['billable'];
//                    $editrow->pricing_model = $status['pricing']['pricing_model'];
//                    $editrow->category = $status['pricing']['category'];
//                }
//                if (isset($status['conversation'])) {
//                    $editrow->conversationid = $status['conversation']['id'];
//                    if (isset($status['conversation']['expiration_timestamp'])) {
//                        $editrow->conversation_expiration_timestamp = $this->_formate_date($status['conversation']['expiration_timestamp']);
//                    }
//                    $editrow->conversation_origin_type = $status['conversation']['origin']['type'];
//                }
//
//                $existing_update = $editrow->tmp_upate_json;
//                $editrow->tmp_upate_json = $existing_update . ",\n" . json_encode($status);
//
//                if ($Table->save($editrow)) {
//                    $this->app->writelog($editrow, "Save Success");
//                } else {
//                    $this->app->writelog($editrow, "Save Failed");
//                }
//
//                if (isset($status['conversation'])) {
//                    $ratingquery = $this->getTableLocator()->get('Ratings')->find();
//                    $ratingquery->where([
//                        ['conversation' => $status['conversation']['id']]
//                    ]);
////                            ->first();
//                    $ratingResults = $ratingquery->all();
//                    //Billing is needed only for Uniq conversation IDS. 
//                    if ($ratingResults->isEmpty()) {
//                        debug("Rating " . $status['conversation']['id']);
//                        $this->app->_rateMe($status);
//                    } else {
//                        // debug($ratingquery);
//
//                        if (!isset($updated[$status['conversation']['id']])) {
//                            // debug("Already Rated updating all fields of " . $status['conversation']['id']);
//                            $streamsTable = $this->getTableLocator()->get('Streams');
//                            $streamsTable->updateAll(
//                                    ['rated' => true],
//                                    ['conversationid' => $status['conversation']['id']]
//                            );
//                            $updated[$status['conversation']['id']] = true;
//                            // debug ("Updated all rated.");
//                            //    debug($updated);
//                        } else {
//                            debug("Already updated ");
//                        }
//                    }
//                }
//            } else {
//                $this->app->writelog($id, "Got null ID for search " . $status['id']);
//            }
//        }
//    }
//
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
//        $this->app->writeinteractive($jsonresponse, "Response from initial menu");
//        curl_close($curl);
//
//        $response = json_decode($jsonresponse, true);
//
//        $table = $this->getTableLocator()->get('Streams');
//        $row = $table->newEmptyEntity();
//
//        $row->contact_stream_id = $this->app->getWastreamsContactId($contactToSend, $FBSettings);
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
//            $this->app->writeinteractive($row, "updated Stream table");
//        } else {
//            $this->app->writeinteractive($record->getErrors(), "Failed to update Stream table");
//        }
//    }
}
