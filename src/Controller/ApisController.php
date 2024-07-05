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

        $formaction = $this->request->getParam('action');

        $this->FormProtection->setConfig('unlockedActions', array(
            $formaction
        ));

        $this->Authentication->allowUnauthenticated(['webhook', 'webhook1', 'sendschedule', 'uploadfile', 'sendmsg']);
    }




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

    

    

    function sendschedule() {
        // debug("Send Schdule");
        $this->viewBuilder()->setLayout('ajax');
        $this->writelog("Whatsapp Schedule function hit", null);
        $data = $this->request->getData();
        ;
        if ($this->request->is('post')) {
            $this->writelog($data, "The is post data");
            $FBsettings = $this->_getFBsettings($data);
            if ($FBsettings['status']['code'] == 200) {
                $this->writelog($FBsettings['status']['code'], "Api Validated");
              //  $data['user_id'] = null;
                //passing the post data to real send funtion.
                $sendQ = $this->getTableLocator()->get('SendQueues');
                $sendQrow = $sendQ->newEmptyEntity();
                $sendQrow->form_data = json_encode($data);
                $sendQrow->status = "queued";
                $sendQ->save($sendQrow);

                http_response_code(200); // Bad Request
                $response['Success'] = 'Message Submitted';
                $this->set('result', $response);
                return;

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

//     function sendchat() {
//         $this->viewBuilder()->setLayout('ajax');
//         $request = $this->getRequest();
//         $data = $this->request->getData();
//         //   debug($data);
//         $authorizationHeader = $request->getHeaderLine('Authorization');
//         if (preg_match('/Bearer\s+(.*)/', $authorizationHeader, $matches)) {
//             $bearerToken = $matches[1];
//         }
//         $data['api_key'] = $bearerToken;
//         $FBSettings = $this->_getFBsettings($data);
//         if ($FBSettings['status']['code'] !== 200) {
//             $result['status'] = "failed";
//             $result['msg'] = "Internal system error, Wrong IP info";
//         } else {
//             //  $contactStream = $this->getTableLocator()->get('ContactStreams')->get($data['mobilenumberId']);
//             $streams_table = $this->getTableLocator()->get('Streams');
//             $streamrow = $streams_table->newEmptyEntity();
// //            $streamrow->schedule_id = $sched_id;
//             $streamrow->contact_stream_id = $data['mobilenumberId'];
//             $streamrow->initiator = "Console";
//             $streamrow->type = "Console";
//             $streamrow->postdata = json_encode($data);
//             $streamrow->account_id = $FBSettings['account_id'];
//             $streams_table->save($streamrow);
//             $contact = $streams_table->get($streamrow->id);
//             $result = $this->_despatch_msg($contact, $data, null, $FBSettings, "text");
//             //debug($result);
//             if (isset($result['messages'][0]['id'])) {
//                 $status['status'] = "success";
//                 $status['msg'] = json_encode($result);
//             } else {
//                 $status['status'] = "failed";
//                 $status['msg'] = json_encode($result);
//             }
//             $this->set('result', $status);
//         }
//     }


}
