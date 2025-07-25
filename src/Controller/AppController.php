<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
//use Cake\Core\Configure;
use Cake\Http\Session\DatabaseSession;
use Cake\Datasource\ConnectionManager;
use App\Service\SlackService;
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    //    public $components = ['Session'];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('FormProtection');
        //        $this->loadComponent('Session');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    // function _ajaxvalidation1($Table, $data)
    // {
    //     //  debug("table is $Table");
    //     $result = [];
    //     $this->loadModel($Table);
    //     $newrow = $this->$Table->newEmptyEntity();
    //     $newrow = $this->$Table->patchEntity($newrow, $data);
    //     $errors = $newrow->getErrors();
    //     //        debug ($data);
    //     //        debug ($errors);
    //     if (empty($errors)) {
    //         return $errors;
    //     } else {
    //         // pr($errors);
    //         foreach ($errors as $key => $val) {
    //             $error['field'] = $key;

    //             foreach ($val as $msgkey => $msgval) {
    //                 $error['error'] = $msgval;
    //             }
    //             $result[] = $error;
    //             $error = array();
    //         }
    //         //    debug ($result);
    //         return $result;
    //     }
    // }

    function _dteditvalidation($Table, $data)
    {
        $result = [];
        $this->loadModel($Table);
        $table = $this->getTableLocator()->get($Table);
        //    debug($data);
        $action = $data['action'];
        $id = array_key_first($data['data']);
        $data = $data['data'][$id];
        if ($action == "edit") {
            $newrow = $table->findById($id)->firstOrFail();
        } else {
            //         debug("checking for new entity");
            $newrow = $table->newEmptyEntity();
        }
        //   debug ($data);
        $newrow = $table->patchEntity($newrow, $data);
        $errors = $newrow->getErrors();
        if (empty($errors)) {
            return $errors;
        } else {
            foreach ($errors as $key => $val) {
                $error['name'] = $key;
                foreach ($val as $msgkey => $msgval) {
                    $error['status'] = $msgval;
                }
                $result['fieldErrors'][] = $error;
                $error = array();
            }
            return $result;
        }
    }

    public function _getsettings($attr = null)
    {
        if (isset($attr)) {
            $query = $this->getTableLocator()->get('Settings')->find();
            $resultsArray = $query
                ->where(['params' => $attr])
                ->toArray();
            if (!empty($resultsArray)) {
                return ($resultsArray[0]->value);
            }
        }
    }

    public function _checkallowed($action = null, $uid = null)
    {
        if (!isset($uid)) {
            $uid = $this->getRequest()->getSession()->read('Auth.User.id');
            //   debug($this->getRequest()->getSession()->read());
        }
        if ($uid == 1) {
            return true;
        }
        $grppermission = $this->getTableLocator()->get('UgroupsPermissions');
        $query = $grppermission->find()
            ->contain(['Permissions'])
            ->where(['permstring' => $action])
            ->contain([
                'Ugroups.Users' => function ($q) use ($uid) {
                    return $q->where(['Users.id' => $uid]);
                }
            ]);
        $number = $query->count();
        if ($number == 0) {
            return false;
        } else {
            return true;
        }
    }

    function _fieldtypes($table_name = null)
    {
        $result = [];
        $flagship = $this->getTableLocator()->get('Flagships');
        $queryarray = $flagship->find()
            ->where(['tbl_name' => $table_name])
            //->toList()
            ->order(['Flagships.order_index ASC']);
        //  pr($query->execute());        
        foreach ($queryarray as $field => $val) {
            // debug((array)$val);
            $row = [];
            $valarray = json_decode(json_encode($val), true);
            $title = $val->title;
            foreach ($valarray as $fkey => $fval) {
                $row[$fkey] = $fval;
            }
            $result[$title] = $row;
        }
        return $result;
    }



    function gen_rand_string($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for (
            $i = 0;
            $i < $length;
            $i++
        ) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function _check_view_permission($view = null, $uid = null)
    {
        if (isset($action)) {
            if (!isset($uid)) {
                $uid = $this->getRequest()->getSession()->read('Auth.User.id');
            }
            if ($uid == 1) {
                return true;
            }
            if (!isset($uid)) {
                return false;
            }
            //            $grppermission = TableRegistry::get('GroupsPermissions');
            $grppermission = $this->getTableLocator()->get('GroupsPermissions');
            $query = $grppermission->find()
                ->contain(['Permissions'])
                ->where(['permstring LIKE' => "view_" . $view . "%"])
                ->contain([
                    'Groups.Users' => function ($q) use ($uid) {
                        return $q->where(['Users.id' => $uid]);
                    }
                ]);
            $number = $query->count();
            if ($number == 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
        return false;
    }

    function validateField($model = null, $field = null, $value = null, $action = "add")
    {
        $tables = $this->getTableLocator()->get($model);
        $table = $tables->newEntity(array($field => $value));
        $result = array();
        if ($table->getError($field)) {
            $error = array_flip($table->getError($field));
            $result['msg'] = array_key_first($error);
            $result['status'] = "failed";
        } else {
            $result['status'] = "success";
        }
        return $result;
    }

    function _genrand($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for (
            $i = 0;
            $i < $length;
            $i++
        ) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
     function sendSlack($text, $severity)
        {
            $slackService = new SlackService();
            // Map severity to icons
            $icons = [
                'info' => ':information_source:',
                'notice' => ':bulb:',
                'warning' => ':warning:',
                'danger' => ':fire:',
                'critical' => ':rotating_light:',
                'success' => ':white_check_mark:'
            ];
            $message = [
                'text' => $text,
                'severity' => $severity,
                'timestamp' => date('Y-m-d H:i:s'),
            ];
            // Pick icon or fallback to info
            $icon = $icons[strtolower($severity)] ?? ':grey_question:';
            $formattedMessage = "{$icon} *[" . strtoupper($severity) . "]* {$text}";;
            return $slackService->sendMessage($formattedMessage);
        }


    function _despatch_msg($streams, $form, $templateQuery, $FBSettings, $type = "template")
    {
        //this section to make sure, camp is followed the stand of skipping blocked number for related account. 
        if ($streams->type == "camp") {
            // Fetch the corresponding contact number from ContactStreams
            $contactnumber = $this->getTableLocator()->get('ContactStreams')->get($streams->contact_stream_id);
            //    debug($FBSettings);
            // Get the BlockedNumbers table
            $Contact_streamTable = $this->getTableLocator()->get('ContactStreams');

            // Check if the number is already blocked by the same account
            $existsblocked = $Contact_streamTable->find()
                ->where([
                    'contact_number' => $contactnumber->contact_number,
                    'account_id' => $FBSettings['account_id'],
                    'camp_blocked' => true
                ])
                ->first();

            if ($existsblocked) {
                $table = $this->getTableLocator()->get('Streams');
                $row = $table->get($streams->id);
                $this->writelog("Error", "Blocked number $contactnumber->contact_number for account id $streams->account_id");
                $row->has_wa = true;
                $row->result = "Blocked number $contactnumber->contact_number for account id $streams->account_id";
                $row->success = false;
                //  $row->sendarray = json_encode($sendarray);
                $table->save($row);
                $slackService = new SlackService();
//                $response['slack']=json_decode($slackService->sendMessage(message: "Blocked number $contactnumber->contact_number for account id $streams->account_id"),true);
                $response['slack']=json_decode($this->sendSlack("Blocked number $contactnumber->contact_number for account id $streams->account_id","warning"),true);

                $response['msg'] = [
                    'status' => 'Error',
                    'message' => "The number $contactnumber->contact_number, is banned  to send camp on account " . $FBSettings['account_id']
                ];
                return $response;

            }
        }

   
        switch ($type) {
            case "template":
                $this->writelog($streams, "Despatching message, streams");
                $this->writelog($form, "Despatching message, Form");
                $this->writelog($templateQuery, "Despatching message, templateQuery");
                //CAP: below is the sample array. we will change the paramers based on form.
                $json_array = '{
                    "messaging_product": "whatsapp",
                    "recipient_type":"individual",
                    "type": "template",
                    "template": {
                        "language": {
                            "policy": "deterministic",
                            "code": "en"
                        },
                        "name": "samsung_freestyle_offer",
                        "components": [
                            {
                                "type": "header",
                                "parameters": [
                                    {
                                        "type": "image",
                                        "image": {
                                            "id": "558075166148411"

                                        }
                                    }
                                ]
                            }, 
                {
                                "type": "body",
                                "parameters": [

                                ]
                            }
                        ]
                    }
                }';
                $sendarray = json_decode($json_array, true);

                $sendarray['template']['components'] = [];
                $bodyarray = [];
                $bodyarray['type'] = "body";
                $bodyarray['parameters'] = [];
              //  debug($form);
                foreach ($form as $key => $val) {
               //    debug($val);
                    $component = [];
                    $param = [];
                    $field_name = $val['field_name'];
                    $keyarray = explode("-", $field_name);
                  //  debug($keyarray);
                    if (($keyarray[0] == "file") && ($keyarray[2] == "header")) {
                        $headerarray['type'] = 'header';
                        $headerarray['parameters'] = [];
                        $component['type'] = $keyarray[2]; //header
                        $param['type'] = $keyarray[3]; //image.
                        if (isset($val['filename'])) {
                            $param[$keyarray[3]]['filename'] = $val['filename'];
                        }
                        $param[$keyarray[3]]['id'] = $val['fbimageid'];
                        $headerarray['parameters'][] = $param;
                    }

                    if ($keyarray[0] == "var") {  //parmeters injection. 
                        $param['type'] = "text";
                        $param['text'] = $val['field_value'];

                        $bodyarray['parameters'][] = $param;
                    }

                    if ($keyarray[0] == "button") {  //parmeters for button variables. 
                 //       debug($keyarray);
                      //  debug($key);
                        $json = '{"type": "button", "sub_type": "url", "index": "0", "parameters": [{"type": "payload", "payload": "btntwo"}]}';
                        $button_array = json_decode($json, true);
                        $button_array['index']=$keyarray[1];
                 //       debug($button_array);
                        $button_array['parameters'][0]['payload'] = $val['field_value'];
                        //   debug($button_array);
                        $sendarray['template']['components'][] = $button_array;
                        // $sendarray=
                    }
                }

                if (isset($headerarray)) {
                    $sendarray['template']['components'][] = $headerarray;
                }

                if (isset($bodyarray)) {
                    $sendarray['template']['components'][] = $bodyarray;
                }

                $mobile = $this->getTableLocator()->get('ContactStreams')->get($streams->contact_stream_id);
                //     debug($mobile->contact_number);

                $sendarray['to'] = $mobile->contact_number;
                $sendarray['template']['name'] = $templateQuery->name;
                $sendarray['template']['language']['code'] = $templateQuery->language;

                $this->writelog($sendarray, "Send array");

                break; 
            case "text":
                $json_array = '{
                        "messaging_product": "whatsapp",
                        "recipient_type": "individual",
                        "to": "966565660638",
                        "type": "text",
                        "text": {
                            "preview_url": false,
                            "body": "MESSAGE_CONTENT"
                        }
                    }';
                $sendarray = json_decode($json_array, true);
                $mobile = $this->getTableLocator()->get('ContactStreams')->get($streams->contact_stream_id);
                //     debug($mobile->contact_number);

                $sendarray['to'] = $mobile->contact_number;
                ///    $sendarray['template']['name'] = $templateQuery->name;
                $sendarray['text']['body'] = $form['message'];

                break;
            case "forward":
            //    debug("This is forward msg");
                $sendarray=$form;
           //     debug($sendarray);
                
                break;
        }

    //    debug($sendarray);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/' . $FBSettings['API_VERSION'] . '/' . $FBSettings['phone_numberId'] . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($sendarray),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBSettings['ACCESSTOKENVALUE'],
            ),
        ));

        $jsonresponse = curl_exec($curl);
        $response= json_decode($jsonresponse, true);


        curl_close($curl);
        $this->writelog($response, "Despatch response");

        $table = $this->getTableLocator()->get('Streams');
        $row = $table->get($streams->id);
        // debug($row);
        //    debug($response);
        if (isset($response['messages'][0]['id'])) {
            $row->messageid = $response['messages'][0]['id'];
            //     $row->type = "send";
            $row->has_wa = true;
            $row->success = true;
            $row->result = $jsonresponse;
            $row->sendarray = json_encode($sendarray);
            $table->save($row);
        } 
        // elseif (isset($response['wa']['messages'][0]['id'])) {
        //     $row->messageid = $response['wa']['messages'][0]['id'];
        //     $row->type = "send";
        //     $row->has_wa = true;
        //     $row->success = true;
        //     $row->result = $jsonresponse;
        //     $table->save($row);
        //} 
        else {
            $this->writelog($response, "Response error");
            $row->has_wa = false;
            $row->result = $jsonresponse;
            $row->success = false;
            $row->sendarray = json_encode($sendarray);
            $table->save($row);
            $slackService = new SlackService();
            $contactnumber = $this->getTableLocator()->get('ContactStreams')->get($streams->contact_stream_id);
            $response['slack'] = json_decode($slackService->sendMessage(message: "Failed to send  $type message to " . $contactnumber->contact_number . " $jsonresponse"), true);
        }
        return $response;
    }

    function writelog($data, $type = null)
    {
        //        if (!$this->_getsettings("log_enabled")) {
        //            return false;
        //        }
        //   print (getenv('LOG'));
        if (intval(getenv('LOG')) == false) {

            //   debug("No logs");
            return false;
        }
        // debug("Logs are enabled");
        $file = LOGS . 'GrandWA' . '.log';
        #  $data =json_encode($event)."\n";  
        $time = date("Y-m-d H:i:s", time());
        $handle = fopen($file, 'a') or die('Cannot open file:  ' . $file); //implicitly creates file
        fwrite($handle, print_r("\n========================$type : $time============================= \n", true));
        fwrite($handle, print_r($data, true));
        fclose($handle);
    }



    function writeinteractive($data, $type)
    {
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

    function getWastreamsContactId($mobile_number, $fbsettings) //function to create/return the WastreamsContactId related to contact number.
    {

        //   debug("Mobile number in getWastreamsContactId is $contact_waid" );
     //   debug($fbsettings);
        $contact_waid = $this->_format_mobile($mobile_number, $fbsettings);
        $contactinfo = $this->getTableLocator()->get('ContactNumbers')->find()->where(['mobile_number' => $contact_waid])->first();
        $contact_stream_table = $this->getTableLocator()->get('ContactStreams');

        $existing = $this->getTableLocator()->get('ContactStreams')->find()->where(['contact_number' => $contact_waid, 'account_id'=>$fbsettings['account_id']])->toArray();
       
        if (empty($existing)) {  //number alreayd exists. 
        //    debug("New");
            $row = $contact_stream_table->newEmptyEntity();
            if (!empty($contactinfo->name)) {
                $row->name = $contactinfo->name;
            }
            $row->contact_number = $contact_waid;
            $row->account_id=$fbsettings['account_id'];       
            if(!isset($user_id)){
                $user_id=1;
            }
            $row->user_id=$user_id;
            if($contact_stream_table->save($row)){
                return $row->id;
            }else{
                debug($row->getErrors());
            }
            
        } else {
        //    debug("Exists");
            $row = $contact_stream_table->get($existing[0]->id);
            if (isset($contactinfo->name)) {
                $row->name = $contactinfo->name;
            }
            $contact_stream_table->save($row);
            return $row->id;
        }
    }

    function updateProfileWastreamsContact($contact_waid, $profile, $FBSettings) //update profile of a WastreamsContactId
    {
        $contact_waid = $this->_format_mobile($contact_waid, $FBSettings);
        $table = $this->getTableLocator()->get('ContactStreams');
        $record = $table->find()->where(['contact_number' => $contact_waid])->toArray();
        if (isset($record)) {
            $row = $table->get($record[0]['id']);

            $row->profile_name = $profile;
            // $row->profile_name = "latheef";
            //      debug($row);
            if ($table->save($row)) {
                //                debug("Saved");
            } else {
          //      debug($row->getError);
            }
        } else {
           // debug("nothign found");
        }
    }

    function _format_mobile($mobile_number, $FBSettings)
    {
    //    debug($FBSettings);
        $country_code = $FBSettings['def_isd'];
        $len = strlen((string) $mobile_number);

        $mobile_number = preg_replace("/^\+/", '', $mobile_number);
        $mobile_number = str_replace('/', '', $mobile_number);
        $mobile_number = str_replace('\\', '', $mobile_number);
        $mobile_number = preg_replace("/\s+/", '', $mobile_number);

        if ($len == 10) {
            $mobile_number = $country_code . $mobile_number;
        }
        return $mobile_number;
    }

    public function _getFBsettings($data)
    {
      //  debug($data);
        //you can either send the uid , api_key, phone_numberId to get fbsettings. 
        if (isset($data['api_key'])) {
            $table = $this->getTableLocator()->get('ApiKeys');
            $apiquery = $table->find()
                ->where(['api_key' => $data['api_key'], 'enabled' => true])
                ->first();
            if (empty($apiquery)) {
                $data['status']['type'] = "Error";
                $data['status']['message'] = "Wrong API Key";
                $data['status']['code'] = 404;
                return $data;
            }
            $acquery = $this->getTableLocator()->get('Accounts')->find();
            $result = $acquery
                ->where(['id' => $apiquery->account_id])
                ->first();
        } elseif (isset($data['user_id'])) {
            //debug($data);
            $table = $this->getTableLocator()->get('Users');
            $userquery = $table->find()
                ->where(['Users.id' => $data['user_id']])
                ->first();
            //  debug($userquery);
            if (empty($userquery)) {
                $data['status']['type'] = "Error";
                $data['status']['message'] = "Wrong user info";
                $data['status']['code'] = 404;
                return $data;
            }
            $acquery = $this->getTableLocator()->get('Accounts')->find();
            $result = $acquery
                ->where(['id' => $userquery->account_id])
                ->first();
        } elseif (isset($data['phone_numberId'])) {
            // debug($data);
            $acquery = $this->getTableLocator()->get('Accounts')->find();
            $result = $acquery
                ->where(['phone_numberId' => $data['phone_numberId']])
                ->first();
        } elseif (isset($data['account_id'])) {
            $acquery = $this->getTableLocator()->get('Accounts')->find();
            $result = $acquery
                ->where(['id' => $data['account_id']])
                ->first();
        }


        // ->toArray();
        if (empty($result)) {
            $data['status']['type'] = "Error";
            $data['status']['message'] = "No related account info found.";
            $data['status']['code'] = 403;
            return $data;
        }


        $data['WBAID'] = $result->WBAID;
        $data['Balance'] = $result->current_balance;
        $data['API_VERSION'] = $result->API_VERSION;
        $data['phone_numberId'] = $result->phone_numberId;
        $data['def_language'] = $result->def_language;
        $data['test_number'] = $result->test_number;
        $data['def_isd'] = $result->def_isd;
        $data['interactive_webhook'] = $result->interactive_webhook;
        $data['interactive_notification_numbers'] = $result->interactive_notification_numbers;
        $data['interactive_api_key'] = $result->interactive_api_key;
        $data['rcv_notification_template'] = $result->rcv_notification_template;
        if (intval(getenv('SEND_MSG')) == true) {
            //   debug("Message enabled ". getenv('SEND_MSG'));
            $data['ACCESSTOKENVALUE'] = $result->ACCESSTOKENVALUE;
        } else {
            // debug("Message disabled ". getenv('SEND_MSG'));
            $data['ACCESSTOKENVALUE'] =  "Message not enabled, current value is " . intval(getenv('SEND_MSG'));
        }

        $data['def_isd'] = $result->def_isd;
        $data['account_id'] = $result->id;
        $data['welcome_msg'] = $result->welcome_msg;
        $data['status']['type'] = "Sucess";
        $data['status']['code'] = 200;
        
        $data['interactive_menu_function'] = $result->interactive_menu_function;
        return $data;
    }

    //Billing and Rating section.



    

    function _rateMedelete($price_array,$fbsettings)
    {
      //  debug ("Rating....");
      //  debug($price_array);
        $this->writelog($price_array, "Rating from rateme");
        $streamTable = $this->getTableLocator()->get('Streams');
        //  debug("Message ID is " . $price_array['id']);
        $record = $streamTable->find()
            ->contain('ContactStreams') // Include the related "ContactStreams" records
            ->where(['messageid' => $price_array['id']])
            ->first();

        if (!$record) {
            // Stop code execution or handle the situation as needed
         //  debug("Record not found");
         //   return false;
            $this->writelog($price_array['id'], "No record found msg id in Streams");
            $this->_notify("No record found msg id " . $price_array['id'], "Warning");
            $return['result']['status'] = "warning";
            $return['result']['message'] = "No record found msg id " . $price_array['id'];
            return $return; // or return; depending on where this code is located
        } else {
         //   debug("Record found in stream");
            $this->writelog($price_array['id'], "Record Found in streams");
        }

      //  return false;


      //  debug($record->conversationid);
        $RatingTable = $this->getTableLocator()->get('Ratings');

            $ratedfromRatingTable = $RatingTable->find()
            ->where(['conversation' => $record->conversationid])
            ->all();    

        if ($ratedfromRatingTable->isEmpty()) {
            //Process charging if not already.
         //   debug("Not already charged: Charging $record->conversationid");
            



            $this->writelog($record, "Passing to Charging");
            $return = $this->_chargeMe($record,$fbsettings);
        } else {
         //   debug("Alrady costed");
        //     debug($ratedfromRatingTable);
             $streamsTable = $this->getTableLocator()->get('Streams');
         //    debug('Updating streams table as rated=true');
             $streamsTable->updateAll(
                 ['rated' => true],
                 ['conversationid' => $record->conversationid]
             );

            $this->writelog($record->conversationid, "Already rated Message ID");
            $return['result']['status'] = "success";
            $return['result']['message'] = "$record->conversationid, Already rated Message ID";
        }
        return $return;
    }


    function _chargeMedelete($record,$fbsettings)
    {
        //debug($record);
        $msgType = $record->type;
        $ph = $record->contact_stream->contact_number;
        $ph = $this->app->_format_mobile($ph, $fbsettings);
        //   debug($ph);
        $countryinfo = $this->_getCountry($ph);
        // debug($countryinfo);
        if (empty($countryinfo)) {
            //    debug("Exiting due to wrong coutnry phone $ph");
            // Log::debug("Country info is empty for $ph");
            $this->_notify("Country info is empty for $ph", "critical");
            return;
        } else {
            //    debug("Contry is $countryinfo->country");
        }
        $msgCategory = $record->category;
        $msgpricing_model = $record->pricing_model;
        $StreamsTable = $this->getTableLocator()->get('Streams');
        $row = $StreamsTable->get($record->id);
        //  debug("msg type is $msgType");
        // switch ($msgType) {
        //     case "send":
        //     case "api":
        //     case "camp":
        //         //    debug("Message type is send");
        $cost = $this->_calculateCost($countryinfo, $msgCategory, $msgpricing_model);
        $cost['cost'] = round($cost['cost'], 2);
        $row->costed = $cost['cost'];
        if ($StreamsTable->save($row)) {
            $result = $this->_updatebalance($row->account_id, $cost['cost']);
            // debug($cost);
            // debug($countryinfo);
            $RatingTable = $this->getTableLocator()->get('Ratings');
            $rating = $RatingTable->newEmptyEntity();
            $rating->stream_id = $record->id;
            $rating->old_balance = $result['old_balance']['current_balance'];
            $rating->new_balance = $result['new_balance']['current_balance'];
            $return['result']['charginginfo']['old_balance'] = $result['old_balance']['current_balance'];
            $return['result']['charginginfo']['new_balance'] = $result['new_balance']['current_balance'];
            $return['result']['charginginfo']['Country'] = $countryinfo->country;
            $rating->cost = $cost['cost'];
            $rating->conversation = $record->conversationid;
            $rating->country = $countryinfo->country;
            $rating->charging_status = $result['status'];
            $rating->tax = $cost['tax'];
            $rating->p_perc = $cost['p_perc'];
            $rating->fb_cost = $cost['fb_cost'];
            $rating->rate_with_tax = $cost['rate_with_tax'];
            if (!$RatingTable->save($rating)) {
                debug($rating->getError);
                $this->_notify(json_encode($rating->getError), "critical");
                $return['result']['message'] = "Charging failed for message type   $msgType with " . $cost['rate_with_tax'];
                $return['result']['status'] = "failed";
            } else {
                $streamsTable = $this->getTableLocator()->get('Streams');
                $streamsTable->updateAll(
                    ['rated' => true],
                    ['conversationid' => $record->conversationid]
                );
                $return['result']['message'] = "Charged message type   $msgType with " . $cost['rate_with_tax'];
                $return['result']['status'] = "sucess";
                //  debug("Rating save  as true for all  record" . $record->conversationid);
            }
        }
        //         break;
        //     case "ISend":
        //      //   debug("processing Isend on covid $record->conversationid");
        //         $return['result']['message'] = "Not Charged for $msgType and updated stream table";
        //         $return['result']['status'] = "success";
        //         $streamsTable = $this->getTableLocator()->get('Streams');
        //         $streamsTable->updateAll(
        //             ['rated' => true],
        //             ['conversationid' => $record->conversationid]
        //         );
        //         break;
        //     default:
        //       //  debug("Not charged for message type $msgType ");
        //         $return['result']['message'] = "Not Charged for $msgType";
        //         $return['result']['status'] = "success";
        //         $streamsTable = $this->getTableLocator()->get('Streams');
        //         $streamsTable->updateAll(
        //             ['rated' => true],
        //             ['conversationid' => $record->conversationid]
        //         );

        //         break;
        // }
        return $return;
    }

    // function _chargeMe($record,$fbsettings)
    // {
    //     //debug($record);
    //     $msgType = $record->type;
    //     $ph = $record->contact_stream->contact_number;
    //     $ph=$this->app->_format_mobile($ph,$fbsettings);
    //  //   debug($ph);
    //     $countryinfo = $this->_getCountry($ph);
    //     // debug($countryinfo);
    //     if (empty($countryinfo)) {
    //       //  debug("Exiting due to wrong coutnry phone $ph");
    //         // Log::debug("Country info is empty for $ph");
    //         $this->_notify("Country info is empty for $ph", "critical");
    //         return;
    //     } else {
    //         // debug($countryinfo->country);
    //     }
    //     $msgCategory = $record->category;
    //     $msgpricing_model = $record->pricing_model;
    //     $StreamsTable = $this->getTableLocator()->get('Streams');
    //     $row = $StreamsTable->get($record->id);
    //     #       debug($msgType);
    //     switch ($msgType) {
    //         case "send":
    //         case "api":
    //         case "camp":
    //             //    debug("Message type is send");
    //             $cost = $this->_calculateCost($countryinfo, $msgCategory, $msgpricing_model);
    //             $cost['cost'] = round($cost['cost'], 2);
    //             $row->costed = $cost['cost'];
    //             if ($StreamsTable->save($row)) {
    //                 $result = $this->_updatebalance($row->account_id, $cost['cost']);
    //                 // debug($cost);
    //                 // debug($countryinfo);
    //                 $RatingTable = $this->getTableLocator()->get('Ratings');
    //                 $rating = $RatingTable->newEmptyEntity();
    //                 $rating->stream_id = $record->id;
    //                 $rating->old_balance = $result['old_balance']['current_balance'];
    //                 $rating->new_balance = $result['new_balance']['current_balance'];
    //                 $return['result']['charginginfo']['old_balance'] = $result['old_balance']['current_balance'];
    //                 $return['result']['charginginfo']['new_balance'] = $result['new_balance']['current_balance'];
    //                 $return['result']['charginginfo']['Country'] = $countryinfo->country;
    //                 $rating->cost = $cost['cost'];
    //                 $rating->conversation = $record->conversationid;
    //                 $rating->country = $countryinfo->country;
    //                 $rating->charging_status = $result['status'];
    //                 $rating->tax = $cost['tax'];
    //                 $rating->p_perc = $cost['p_perc'];
    //                 $rating->fb_cost = $cost['fb_cost'];
    //                 $rating->rate_with_tax = $cost['rate_with_tax'];
    //                 if (!$RatingTable->save($rating)) {
    //                     debug($rating->getError);
    //                     $this->_notify(json_encode($rating->getError), "critical");
    //                     $return['result']['message'] = "Charging failed for message type   $msgType with " . $cost['rate_with_tax'];
    //                     $return['result']['status'] = "failed";
    //                 } else {
    //                     $streamsTable = $this->getTableLocator()->get('Streams');
    //                     $streamsTable->updateAll(
    //                         ['rated' => true],
    //                         ['conversationid' => $record->conversationid]
    //                     );
    //                     $return['result']['message'] = "Charged message type   $msgType with " . $cost['rate_with_tax'];
    //                     $return['result']['status'] = "sucess";
    //                     //  debug("Rating save  as true for all  record" . $record->conversationid);
    //                 }
    //             }
    //             break;
    //         case "ISend":
    //             $return['result']['message'] = "Not Charged for $msgType and updated stream table";
    //             $return['result']['status'] = "success";
    //             $streamsTable = $this->getTableLocator()->get('Streams');
    //             $streamsTable->updateAll(
    //                 ['rated' => true],
    //                 ['conversationid' => $record->conversationid]
    //             );
    //             break;
    //         default:
    //             debug("Not charged for message type $msgType ");
    //             $return['result']['message'] = "Not Charged for $msgType";
    //             $return['result']['status'] = "success";

    //             break;
    //     }
    //     return $return;
    // }


    function _calculateCost($countryinfo, $msgCategory, $msgpricing_model)
    {

        //debug($countryinfo);
        $cost = [];
        $tax_perc = $this->_getsettings('tax');
        $profit_perc = $this->_getsettings('profit_margin');
        $fb_cost = $countryinfo->$msgCategory;
        $rate_with_tax = ($fb_cost * ($tax_perc / 100)) + $fb_cost;
        $customer_rate_single = ($rate_with_tax * ($profit_perc / 100)) + $rate_with_tax;
        $cost['tax'] = $tax_perc;
        $cost['p_perc'] = $profit_perc;
        $cost['fb_cost'] = $fb_cost;
        $cost['rate_with_tax'] = $rate_with_tax;
        $cost['cost'] = $customer_rate_single;
        return $cost;
    }

    function _updatebalance($account_id, $cost)
    {
        //UPDATE `streams` SET `cost` = '0' WHERE `streams`.`id` = 58588; 
        $result = [];
        $accountTable = $this->getTableLocator()->get('Accounts');
        $result['old_balance'] = $accountTable->get($account_id)->toArray();
        //    debug($old_balance);
        $result['status'] = 1;

        // debug("updating $account_id with cost of $cost");
        $connection = ConnectionManager::get('default');

        try {
            // Begin the transaction
            $connection->begin();

            // Lock the table
            $connection->execute('LOCK TABLES accounts WRITE');

            //  debug("Locking table to update $cost");
            // Update the balance column
            $query = $connection->newQuery();
            $query->update('accounts')
                ->set(['current_balance' => $query->newExpr('current_balance - :cost')])
                ->bind(':cost', $cost, 'float')
                ->where(['id' => $account_id])
                ->execute();

            // debug($query);
            // debug("updating the balance");
            // Unlock the table
            //   debug($query->sql());
            $connection->execute('UNLOCK TABLES');

            // Commit the transaction
            $connection->commit();
        } catch (\Exception $e) {
            $result['status'] = 10;
        //    debug($e);
         //   debug("Rolling back");
            // Rollback the transaction in case of an error
            $connection->rollback();
            // Handle the error appropriately
        }

        $result['new_balance'] = $accountTable->get($account_id)->toArray();
        return $result;
    }

    function _getCountry($ph = null)
    {
        //   debug($contact);
        //    $ph = "972345449595050";
        if (strlen($ph) === 12) {
        }
        //$this->_format_mobile($ph, $data)
        $Country = [];

        $this->writelog($ph, "phone number");

        $pricaTable = $this->getTableLocator()->get('PriceCards');
        $codes = $pricaTable->find()
            ->order(['country_code DESC'])
            ->all();

        foreach ($codes as $key => $val) {
            $this->writelog($val, "Current code array");
            if (substr($ph, 0, strlen((string) $val->country_code)) == $val->country_code) {
                $Country = $val;
                break;
            }
        }
        return $Country;

        //  debug($Country);
    }

    function viewRcvImg($file_id = null, $filetype = null)
    {

      //  $file_id = "6371848519559997";
     //   $filetype = "image/jpeg";
        //        $session = $this->request->getSession();
        $data['account_id'] = $this->getMyAccountID();
        $FBsettings = $this->_getFBsettings($data);

        $this->viewBuilder()->setLayout('ajax');
        $file = tmpfile();
        $file_path = stream_get_meta_data($file)['uri'];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/' . $file_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: ' . $filetype,
                'Authorization: Bearer ' . $FBsettings['ACCESSTOKENVALUE']
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        $url = $result['url'];
        // debug($url);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_HEADER => 0,
            CURLOPT_ENCODING => '',
            //            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            //            CURLOPT_FILE => $file_handle,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBsettings['ACCESSTOKENVALUE'],
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
            ),
        ));

        $raw = curl_exec($curl);

        curl_close($curl);
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $file_handle = fopen($file_path, 'x');

        fwrite($file_handle, $raw);

        fclose($file_handle);
        $response = $this->response->withFile(
            $file_path,
            ['download' => true, 'name' => "myfilename"]
        );
        $response->withType($filetype);
        return $response;
    }

    public function _notify($message, $severity)
    {
        //$functionName = __FUNCTION__;
        //$lineNumber = __LINE__;


        $backtrace = debug_backtrace();

        if (isset($backtrace[1])) {
            $function = $backtrace[1]['function'];
            $linenumber = $backtrace[1]['line'];
        } else {
            $function = null;
            $linenumber = null;
        }

        $table = $this->getTableLocator()->get('Notifications');
        $newrow = $table->newEmptyEntity();
        $newrow->line = $linenumber;
        $newrow->function = $function;
        $newrow->details = $message;
        $newrow->severity = $severity;
        $table->save($newrow);
    }

    public function getMyAccountID()
    {
        $user = $this->Authentication->getIdentity();
        if ($user) {
            $session = $this->request->getSession();
            return $session->read('Config.account_id');
            //   return $account_id;
        } else {
            // User is not authenticated
        }
    }

    public function getMyUID()
    {
        $user = $this->Authentication->getIdentity();
        if ($user) {
            return $user->id;
        } else {
            // User is not authenticated
        }
    }

    public function getMyUserName()
    {
        $user = $this->Authentication->getIdentity();
        if ($user) {
            return $user->name;
        } else {
            return null;
        }
    }

    public function getMyGID()
    {
        $user = $this->Authentication->getIdentity();
        if ($user) {
            return $user->ugroup_id;
        } else {
            // User is not authenticated
        }
    }

    public function getMyAPIKey($account_id = null)
    {
        if (!isset($account_id)) {
            $account_id = $this->getMyAccountID();
        }
        $table = $this->getTableLocator()->get('ApiKeys');
        $apiquery = $table->find()
            ->where(['account_id' => $account_id, 'enabled' => true])
            ->first();
        if (empty($apiquery)) {
            return false;
        } else {
            return $apiquery->api_key;
        }
    }

    public function getMyMobileNumber($user_id = null)
    {
        if (!isset($user_id)) {
            $user_id = $this->getMyUID();
        }
      //  debug($user_id);
        $table = $this->getTableLocator()->get('Users');
        $userquery = $table->find()
            ->where(['id' => $user_id, 'active' => true])
            ->first();
        if (empty($userquery)) {
            return false;
        } else {
            return $userquery->mobile_number;
        }
    }

    function resend($id = null) {
        $this->viewBuilder()->setLayout('ajax');
        $stream = $this->getTableLocator()->get('Streams')->find()->where(['id' => $id])->first();
        $data['account_id'] = $stream->account_id;
        $FBSettings = $this->_getFBsettings($data);
        $this->writelog($id, "Resending MSG");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/' . $FBSettings['API_VERSION'] . '/' . $FBSettings['phone_numberId'] . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $stream->sendarray,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBSettings['ACCESSTOKENVALUE'],
            ),
        ));

        $jsonresponse = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($jsonresponse, true);
        if (isset($response['error'])) {
            $result['status'] = "failed";
            $result['msg'] = $response['error']['message'];
        } else {
            $result['status'] = "success";
            //       $result['msg']=$response['error']['message'];
        }
        //  debug($response);
     //      debug($response);
        $this->writelog($response, "Despatch response of Resend msg");
//
        $table = $this->getTableLocator()->get('Streams');
        $row = $table->get($id);
        if (isset($response['messages'][0]['id'])) {
            $row->messageid = $response['messages'][0]['id'];
            $row->type = "send";
            $row->has_wa = true;
            $row->success = true;
            $row->result = $jsonresponse;
            $table->save($row);
        } elseif(isset($response['wa']['messages'][0]['id'])) {
            $row->messageid = $response['wa']['messages'][0]['id'];
            $row->type = "send";
            $row->has_wa = true;
            $row->success = true;
            $row->result = $jsonresponse;
            $table->save($row);
        }else{
            $this->writelog($response, "Response error");
            $row->type = "send";
            $row->has_wa = false;
            $row->result = $jsonresponse;
            $row->success = false;
            //  $row->sendarray = json_encode($sendarray);
            $table->save($row);
        }

        $this->set('result', $result);
    }


    function createSHAHash($data){
        $privateKey="bW9oYW1tZWQ";
        $dataWithPrivateKey = $data . $privateKey;
        $shaHash = hash('sha256', $dataWithPrivateKey);
     //   debug($shaHash);
        return $shaHash;

    }

    function validateSHAHash($data, $hashToValidate) {
        $privateKey="bW9oYW1tZWQ";
        // Generate SHA hash using the provided data and private key
        $generatedHash = $this->createSHAHash($data, $privateKey);
        // Compare generated hash with the hash to validate
        return hash_equals($generatedHash, $hashToValidate);
    }


    // function register(){
    //     debug("Register");
    // }
}
