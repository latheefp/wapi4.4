<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Hash;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\Auth\DefaultPasswordHasher;
//use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use Cake\Event\Event;
//use Cake\Http\Exception\ForbiddenException;
//use Cake\Datasource\ConnectionManager;
//use Cake\Cache\Cache;
use Cake\ORM\Query;
use Twig\TokenParser\EmbedTokenParser;

class TestsController extends AppController
{

    function test()
    {
        $this->viewBuilder()->setLayout('ajax');
        $cmd = ROOT . DS . "bin/cake upload -i 33 > /dev/null 2>&1 &";
        debug($cmd);
        $cmd = ROOT . DS . "bin/contactupload.sh 33 > /dev/null 2>&1 &";
        debug($cmd);
        exec($cmd);
  
        $output = shell_exec($cmd);

        debug($output);
    }

    function redistest()
    {
        $lockKey = "abc2343243434234";

        $result = Cache::add($lockKey, true);
        //debug($result);
        if (!$result) {
            debug("Added");
            return;
        } else {
            debug("Failed to add");
            Cache::delete($lockKey);
        }
    }

    function testbg()
    {
        $pid = pcntl_fork();

        if ($pid == -1) {
            die("Could not fork.");
        } elseif ($pid) {
            // Parent process
            echo "Parent process\n";
            // You can continue with other tasks in the parent process
        } else {
            // Child process
            echo "Child process\n";
            $this->bgfunction(); // Run your function in the child process
            exit(0);
        }
    }

    function bgfunction()
    {
        // Your function's code goes here
        for ($i = 1; $i <= 10; $i++) {
            debug("Iteration $i\n");
            sleep(1); // Simulate some work
        }
    }

    public function isAuthorized($user)
    {
        return true;
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        //        $this->Security->setConfig('unlockedActions', ['getdata', 'edit',]);
        //        $this->Auth->allow(['ratecard', 'apiEndpoint']);
    }

    public function index()
    {
        $this->viewBuilder()->setLayout('ajax');
    }

    // Sample API endpoint
    public function apiEndpoint()
    {
        $apiKey = $this->request->getHeaderLine('X-API-Key'); // Assuming API key is provided in the request header
        // Check if the API key is correct
        if ($this->_validatekey($apiKey)) {
            throw new ForbiddenException('Invalid API key'); // Throws a 403 Forbidden exception
        }

        // Your API logic here
        // Return success response
        $response = [
            'status' => 'success',
            'message' => 'API call successful',
            // ...
        ];
        $this->set(compact('response'));
        $this->viewBuilder()->setOption('serialize', 'response');
    }

    function _validatekey($data)
    {
        $result = [];
        $table = $this->getTableLocator()->get('ApiKeys');
        $query = $table->find()
            ->where(['api_key' => $data['api_key'], 'enabled' => true])
            ->first();
        if (empty($query)) {
            $result['status'] = false;
            $result['msg'] = "Wrong API KEY";
        } else {
            $result['status'] = true;
            $result['user_id'] = $query->user_id;

            if (!$this->_checkallowed('manage_camp', $query->user_id)) {
                $result['status'] = false;
                $result['user_id'] = $query->user_id;
                $result['msg'] = "Permission denied";
                return $result;
            }
        }

        return $result;
    }

    function _getCountry($ph = null)
    {
        //   debug($contact);
        //    $ph = "972345449595050";

        $pricaTable = $this->getTableLocator()->get('PriceCards');
        $codes = $pricaTable->find()
            ->order(['country_code DESC'])
            ->all();

        foreach ($codes as $key => $val) {
            if (substr($ph, 0, strlen($val->country_code)) == $val->country_code) {
                $Country = $val;
                break;
            }
        }
        return $Country;

        //  debug($Country);
    }

    function rateme($price_array)
    {
        // $pricing_json = '{"id":"wamid.HBgMOTE5NDk2NDcwODA0FQIAERgSQzA5NzA3NzZDNTBCREJCQjA3AA==","status":"sent","timestamp":"1686250127","recipient_id":"919496470804","conversation":{"id":"eadc0fa322314cefd424e85dbfe1a258","expiration_timestamp":"1686336540","origin":{"type":"marketing"}},"pricing":{"billable":true,"pricing_model":"CBP","category":"marketing"}}';
        // $price_array = json_decode($pricing_json, true);
        //   debug($price_array);
        //Get Steams ID to proceed further. 
        $streamTable = $this->getTableLocator()->get('Streams');
        $record = $streamTable->find()
            ->contain('ContactStreams') // Include the related "ContactStreams" records
            ->where(['messageid' => $price_array['id']])
            // ->toArray()
            ->first();
        //debug($record->toArray());
        //if the same conversation_id is charged before, dont charge, set it zero.
        //  debug("Checking $record->conversationid");
        //only devlivered message will be charged, there should be cronjob to charge all messsage delivered later.
        $existingPaidConvID = $streamTable->find()
            ->where([
                'conversationid' => $record->conversationid,
                'cost > ' => 0,
                'delivered_time IS NOT NULL'
            ])
            ->count();
        if ($existingPaidConvID == 0) { //nothing is costed yet. 
            $this->_chargeMe($record);
        } else {
            //   debug($record->conversationid . " is already rated");
        }
    }

    function _chargeMe($record)
    {
        //  debug($record);
        $msgType = $record->type;
        $ph = $record->contact_stream->contact_number;
        $countryinfo = $this->_getCountry($ph);
        $msgCategory = $record->category;
        $msgpricing_model = $record->pricing_model;
        $StreamsTable = $this->getTableLocator()->get('Streams');
        $row = $StreamsTable->get($record->id);
        switch ($msgType) {
            case "send":
                $cost = $this->_calculateCost($countryinfo, $msgCategory, $msgpricing_model);
                $cost['cost'] = round($cost['cost'], 2);
                $row->cost = $cost['cost'];
                if ($StreamsTable->save($row)) {
                    $result = $this->_updatebalance($row->account_id, $cost['cost']);
                    //             debug($cost);
                    //  debug($countryinfo);
                    $RatingTable = $this->getTableLocator()->get('Ratings');
                    $rating = $RatingTable->newEmptyEntity();
                    $rating->stream_id = $record->id;
                    $rating->old_balance = $result['old_balance']['current_balance'];
                    $rating->new_balance = $result['new_balance']['current_balance'];
                    $rating->cost = $cost['cost'];
                    $rating->country = $countryinfo->country;
                    $rating->charging_status = $result['status'];
                    $rating->tax = $cost['tax'];
                    $rating->p_perc = $cost['p_perc'];
                    $rating->fb_cost = $cost['fb_cost'];
                    $rating->rate_with_tax = $cost['rate_with_tax'];

                    $RatingTable->save($rating);
                    //  debug($countryinfo);
                }

                break;
            default:
                debug($msgType);
                break;
        }
    }

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

    function updatebalance()
    {
        $StreamsTable = $this->getTableLocator()->get('Streams');
        $row = $StreamsTable->find()
            ->where(function ($exp, $q) {
                return $exp->isNotNull('tmp_upate_json');
            })
            ->all();
        foreach ($row as $key => $val) {
            $data = trim($val->tmp_upate_json, ',');
            //   debug($data);
            $jsonArray = explode("\n", $data);
            foreach ($jsonArray as $jkey => $jval) {
                if (!empty($jval)) {
                    //    debug($jval);
                    $jval = trim($jval, ',');
                    $price_array = json_decode($jval, true);
                    if (isset($price_array['pricing'])) {
                        $this->_rateMe($price_array);  //function from appController
                    }
                }
            }
        }
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

            debug("Locking table to update $cost");

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
            debug($query->sql());
            $connection->execute('UNLOCK TABLES');

            // Commit the transaction
            $connection->commit();
        } catch (\Exception $e) {
            $result['status'] = 10;
            debug($e);
            debug("Rolling back");
            // Rollback the transaction in case of an error
            $connection->rollback();
            // Handle the error appropriately
        }

        $result['new_balance'] = $accountTable->get($account_id)->toArray();
        return $result;
    }

    function setredis()
    {
        Cache::write('rcv_q_count', 0);
    }

    function writeredis()
    {
        Cache::increment('rcv_q_count');
        $posts = [
            'name' => 'latheef'
        ];
        Cache::write('posts', $posts);
    }

    function readredis()
    {



        debug(Cache::read('posts'));
        debug(Cache::read('rcv_q_count'));
    }

    function txn()
    {
        $connection = ConnectionManager::get('default');
        $connection->begin();

        $connection->execute('UPDATE rcv_queues SET status = ? WHERE id = ?', ["txn", 2]);
        // sleep(30);
        $connection->commit();
    }

    function txn1()
    {
        $lockTimeout = 3; // Example: 2 seconds
        $connection = ConnectionManager::get('default');
        try {
            // Attempt to begin a transaction with a lock timeout
            $connection->begin(['timeout' => $lockTimeout]);
            $stmt = $connection->execute('UPDATE rcv_queues SET status = ? WHERE id = ? AND status = ?', ["lathef", 2, 'txn1']);
            debug($stmt);
            $affectedRows = $stmt->rowCount();
            if ($connection->commit()) {
                if ($affectedRows > 0) {
                    echo "Transaction committed successfully. {$affectedRows} rows were affected.";
                } else {
                    echo "Transaction committed, but no rows were affected.";
                }
            } else {
                echo "Transaction failed to commit. Database changes not applied.";
            }
        } catch (\PDOException $e) {
            $connection->rollback();
            echo "Database operation failed: " . $e->getMessage();
            debug("failed");
        }
    }

    function test2()
    {

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

    function savemetric($module, $account_id = 0, $value)
    {
        $metricTable = $this->getTableLocator()->get('Metrics');
        $newRow = $metricTable->newEmptyEntity();
        $newRow->module_name = $module;
        $newRow->metric_value = $value;
        $newRow->account = $account_id;
      //  debug($newRow);
      if (!$metricTable->save($newRow)) {
        $errors = $newRow->getErrors();
        debug("Save failed. Errors: " . print_r($errors, true));
    } else {
        debug("Save Success");
    }
    }


    function forwarderQ($mobile_number="00966547237272", $stream_id = 170176) {
        $sendQData['mobile_number']=$mobile_number;
        $sendQData['type']="forward";
        $sendQData['api_key']=$this->getMyAPIKey($this->getMyAccountID());
        $sendQData['stream_id']=stream_id;
        $sendQ = $this->getTableLocator()->get('SendQueues');
        $sendQrow = $sendQ->newEmptyEntity();
        $sendQrow->form_data = json_encode($sendQData);
        $sendQrow->status = "queued";
        $result=[];
        if($sendQ->save($sendQrow)){
            $result['status']="success";
            $result['msg']="Message queued for delivery, $sendQrow->id";
        }else{
            $result['status']="failed";
            $result['msg']="Failed to forward";

        }

        $this->set('result',$result);
    }


    function printmobile(){
        debug($this->getMyMobileNumber());
    }


    function forwarder($mobile_number="00966547237272", $id = 170176) {

        $streams=$this->getTableLocator()->get('Streams')->get($id);
     //   debug($streams->recievearray);
        $msgArray = json_decode($streams->recievearray, true);

        $message=$msgArray['entry'][0]['changes'][0]['value']['messages'][0];

        if(empty($message)){
            debug ("Error: empty message");
            return false;
        }

        $sendarrayJson='{
            "to": "966547237272",
            "messaging_product": "whatsapp",
            "recipient_type": "individual"
        }';


        $sendarray = json_decode($sendarrayJson, true);
        $type=$message['type'];
        $sendarray['type']=$type;
        $sendarray['api_key']=$this->getMyAPIKey($this->getMyAccountID());
        $sendarray['mobile_number']=$mobile_number;
        
        $payload=[];

        switch ($type) {
            case "image":
               $payload['id']=$message[$type]['id'];
                break;
            case "document":
                 $payload['id']=$message[$type]['id'];
                break;
            case "video":
                $payload['id']=$message[$type]['id'];
                break;
            case "text":
                $payload['body']=$message[$type]['body'];
                break;
            case "location":
                $result = $result . "https://maps.google.com/?q=" . $val['location']['latitude'] . "," . $val['location']['longitude'];
                break;
            case "sticker":
                $result = $result . '<img  width="512" height="512" src="/campaigns/viewrcvImage?fileid=' . $val['sticker']['id'] . '&type=' . $val['sticker']['mime_type'] . '&id=' . $data['id'] . '">';
                break;
            case "interactive":
                $result = $result . "Interactive Reply:" . $val['interactive']['list_reply']['title'] . ":" . $val['interactive']['list_reply']['description'];
                break;
            case "audio":
                $result = $result . '<div class="audio-message"><div class="audio-player"><audio id="audioPlayer" controls>';
                $result = $result . '<source src="/campaigns/viewrcvImage?fileid=' . $val['audio']['id'] . '&type=' . $val['audio']['mime_type'] . '&id=' . $data['id'] . '" type="audio/mpeg">';
                $result = $result . 'Your browser does not support the audio element.</audio></div>';
                $result = $result . '<div class="play-button"> <button id="playButton" onclick="togglePlayback()"></button> </div> </div>';
                break;
            case "reaction":
                $result = $result . "Reaction:" . $val['reaction']['emoji'];
                break;
            case "contacts":
                $i = 0;
                $result = $result . ' <div class="container"><div class="table-responsive">';
                foreach ($val['contacts'] as $ckey => $cval) {
                    $i++;
                    $result = $result . "<h4>Shared Contact: $i </h4><table class='table table-boarderd col-md-6'>";
                    if (isset($cval['name']['first_name'])) {
                        $result = $result . "<tr><td><b>First Name</b></td><td>" . $cval['name']['first_name'] . "</td></tr>";
                    }

                    if (isset($cval['org']['company'])) {
                        $result = $result . "<tr><td><b>Organization</b></td><td>" . $cval['org']['company'] . "</td></tr>";
                    }


                    $result = $result . "<tr><td><b>Last Name</b></td><td>" . $cval['name']['last_name'] . "</td></tr>";
                    $result = $result . "<tr><td><b>Formated Name</b></td><td>" . $cval['name']['formatted_name'] . "</td></tr>";

                    foreach ($cval['phones'] as $key => $val) {
                        $result = $result . "<tr><td><b>" . $val['type'] . "</b></td><td>" . $val['phone'] . "</td></tr>";
                    }
                    $result = $result . "</table>";
                    $result = $result . "<br>";
                }
                $result = $result . "</div></div>";

                break;
        }

        $sendarray[$type]=$payload;

        debug($message);
        debug($sendarray);



        






    //     $data['account_id'] = $this->getMyAccountID();
    //   //  debug($data);
    //     $FBSettings = $this->_getFBsettings($data);
    //     debug($FBSettings);
    //     if ($FBSettings['status']['code'] !== 200) {
    //         $result['status'] = "failed";
    //         $result['msg'] = "Internal system error, Wrong IP info";
    //     } else {
           

    //     }







// Now, $msgArray contains the JSON data as a PHP array
    }



    
}
