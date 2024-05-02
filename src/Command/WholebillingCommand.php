<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//use PhpOffice\PhpSpreadsheet\Helper;
//use PhpOffice\PhpSpreadsheet\IOFactory;
//use Cake\ORM\TableRegistry;

use Cake\Utility\Hash;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\Auth\DefaultPasswordHasher;
//use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use Cake\Event\Event;
//use Cake\Http\Exception\ForbiddenException;
use Cake\Datasource\ConnectionManager;
//use Cake\Cache\Cache;
use Cake\ORM\Query;
use Twig\TokenParser\EmbedTokenParser;
use App\Controller\AppController; //(path to your controller).

/**
 * Upload command.
 */
class WholebillingCommand extends Command {

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
                   'stream_id' => [
                       'short' => 'i',
                       'help' => 'Stream ID to bill',
                       'required' => false,
                   ]
                ]
        );

        return $parser;
    }

    
    public function initialize(): void {
        parent::initialize();
        $this->app = new AppController();
        
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $stream_id = $args->getOption('stream_id');
        if (isset($stream_id)) {
            $StreamsTable = $this->getTableLocator()->get('Streams');
            $val = $StreamsTable->get($stream_id);
            if($val->rated == true){
                debug ("$stream_id is alrady rated");
               // return true;
            }else{
                $this->updateSingleBalance($val);
            }
           
        } else {
            $this->updatebalance();
        }
    }

    function updatebalance()
    {
        $StreamsTable = $this->getTableLocator()->get('Streams');
        $row = $StreamsTable->find()
            ->where(
                function ($exp, $q) {
                    return $exp->isNotNull('tmp_upate_json');
                }
            )
            ->andWhere(['tmp_upate_json LIKE' => "%%pricing%%"])
            ->andWhere(['rated' => 0, 'success' => true])
            // ->andWhere(function ($exp, $q) {
            //     return $exp->between('created', '2024-01-01', '2024-12-31');
            // })
            // ->andWhere(function ($exp, $q) {
            //     return $exp->in('type', ['send', 'api', 'camp']); //isend, welcome, receive and foward is not charced.  select DISTINCT streams.type from streams; 
            // })
            ->all();
        $total = $row->count();
        debug("Running Rating on $total Records");
        // return false;
          sleep(10);
        $i = 0;
        foreach ($row as $key => $val) {
            //        // debug($val);
            $i++;
            $perc = round($i / $total * 100);
            print("Completed $perc %");


            $this->updateSingleBalance($val);
            // return false;
        }
    }


    function updateSingleBalance($val)
    {
        debug($val);

      #  debug($val->tmp_upate_json);
        $data = trim($val->tmp_upate_json, ',');

        $jsonArray = explode("\n", $data);
        debug($jsonArray);
        //   debug($val->account_id);
        $account_info['account_id'] = $val->account_id;
        $fbsettings = $this->app->_getFBsettings($account_info);
        //      debug($fbsettings);
        if ($fbsettings['status']['code'] != 200) {
       #     debug("Wrong fbsettings for account id $val->account_id");
            return false;
        }
        //     debug($fbsettings);
        $pricing=false;
        foreach ($jsonArray as $jkey => $jval) {
            if (!empty($jval)) {
             //   debug($jval);
                $jval = trim($jval, ',');
                $price_array = json_decode($jval, true);
               // debug($price_array);
                if (isset($price_array['pricing'])) {
            //        debug("Rating price array");
                    //   debug($price_array);
                    $pricing=true;
                    $this->_rateMe($price_array, $fbsettings);  //function from appController
                    continue; #once pricing array found, no more looping needed on json array. 
                } 
            }

           
        }

        if(!$pricing){
            debug("No pricing array found in json.");
            
        }
    }



    function _rateMe($price_array,$fbsettings)
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

    function _chargeMe($record,$fbsettings)
    {
        //debug($record);
        $msgType = $record->type;
        $ph = $record->contact_stream->contact_number;
        $ph=$this->app->_format_mobile($ph,$fbsettings);
     //   debug($ph);
        $countryinfo = $this->_getCountry($ph);
        // debug($countryinfo);
        if (empty($countryinfo)) {
        //    debug("Exiting due to wrong coutnry phone $ph");
            // Log::debug("Country info is empty for $ph");
            $this->_notify("Country info is empty for $ph", "critical");
            return;
        }else{
        //    debug("Contry is $countryinfo->country");
        }
        $msgCategory = $record->category;
        $msgpricing_model = $record->pricing_model;
        $StreamsTable = $this->getTableLocator()->get('Streams');
        $row = $StreamsTable->get($record->id);
      //  debug("msg type is $msgType");
        switch ($msgType) {
            case "send":
            case "api":
            case "camp":
                //    debug("Message type is send");
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
                break;
            case "ISend":
             //   debug("processing Isend on covid $record->conversationid");
                $return['result']['message'] = "Not Charged for $msgType and updated stream table";
                $return['result']['status'] = "success";
                $streamsTable = $this->getTableLocator()->get('Streams');
                $streamsTable->updateAll(
                    ['rated' => true],
                    ['conversationid' => $record->conversationid]
                );
                break;
            default:
              //  debug("Not charged for message type $msgType ");
                $return['result']['message'] = "Not Charged for $msgType";
                $return['result']['status'] = "success";
                $streamsTable = $this->getTableLocator()->get('Streams');
                $streamsTable->updateAll(
                    ['rated' => true],
                    ['conversationid' => $record->conversationid]
                );

                break;
        }
        return $return;
    }

    
 
    function _getCountry($ph = null)
    {
    //       debug($ph);
        //    $ph = "972345449595050";
        $Country=[];
        $pricaTable = $this->getTableLocator()->get('PriceCards');
        $codes = $pricaTable->find()
            ->order(['country_code DESC'])
            ->all();


         //   debug($codes);

        foreach ($codes as $key => $val) {
        //    debug($val);
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
                'costed > ' => 0,
                'delivered_time IS NOT NULL'
            ])
            ->count();
        if ($existingPaidConvID == 0) { //nothing is costed yet. 
            $this->_chargeMe($record);
        } else {
            //   debug($record->conversationid . " is already rated");
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

        //    debug("Locking table to update $cost");

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
   //         debug($query->sql());
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
}
