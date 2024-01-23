<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\FrozenTime; // Import FrozenTime
use App\Controller\AppController; //(path to your controller).
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;


class InvoiceCommand extends Command
{

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        $parser->addOptions(
             [
                'account_id' => [
                    'short' => 'a',
                    'help' => 'Account ID',
                    'required' => false,
                ],
                'month' => [
                    'short' => 'm',
                    'help' => 'Month',
                    'required' => false,
                ],
                'year' => [
                    'short' => 'y',
                    'help' => 'Year',
                    'required' => false,
                ],
            

             ]
        );

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $account_id = $args->getOption('account_id');
        $month = $args->getOption('month');
        $year = $args->getOption('year');

       

        if(!isset($month)){
            debug("$month is empty");
            $currentMonth = FrozenTime::now()->month;
            if($currentMonth==1){
                $month=12;
            }else{
                $month=$currentMonth-1;
            }
        }

        if(!isset($year)){
            //year is last month  year. 
            $currentMonth = FrozenTime::now()->month;
            if($currentMonth==1){
                $month=12;
            }else{
                $month=$currentMonth-1;
            }
            $currentyear = FrozenTime::now()->year;

            if($month == 12){
                $year=$currentyear-1  ;
            }else{
                $year= $currentyear;
            }
        }

        if(isset($account_id)){
            $this->genInvoice($account_id, $month, $year);
        }else{
            $accountTable=$this->getTableLocator()->get('Accounts');
            $accounts=$accountTable->find()->select(['id'])->all();
            foreach($accounts as $key => $val){
                $this->genInvoice($val->id, $month, $year);
           }

        }

       

    }


    public function initialize(): void
    {
        parent::initialize();
        $this->app = new AppController();
    }

    function check_invoice_status($account_id,$month, $year){
       
        //TODO: add logic to check already invoiced.
        return true;
    }

    function genInvoice($account_id,$month, $year){
        debug("Invoicing for $month-$year for $account_id");
        $invoiceTable = $this->getTableLocator()->get('Invoices');
        $existing=$invoiceTable->find()
                ->where(['account_id'=>$account_id,'month'=>$month,'year'=>$year])
                ->first();


      //  debug($existing);    
        
        if(isset($existing)){
            debug("Invoice Already existing for customer_id $account_id for $month-$year");
            return false;
        }

                



        $ratingsTable=$this->getTableLocator()->get('Ratings');
        //update the  ratings table records are invoiced agianst record
        // 1. Retrieve the matching records:
        $matchingRecords = $ratingsTable
            ->find()
            ->where([
                'MONTH(Ratings.created)' => $month,
                'YEAR(Ratings.created)' => $year,
                'Streams.account_id' => $account_id
            ])
            ->contain(['Streams']);

        // 2. Update the `invoiced` field for all matching records:
        $totalCost=0;
        foreach ($matchingRecords as $record) {
            $record->invoiced = true;
            $ratingsTable->save($record);
            $totalCost=$totalCost+$record->cost;
        }

       
        $newinvoince = $invoiceTable->newEmptyEntity();
        $newinvoince->account_id = $account_id;
        $newinvoince->invoince_number = 123;
        $newinvoince->total_amount = $totalCost;
        $newinvoince->year=$year;
        $newinvoince->month=$month;
        if ($totalCost < 1) {
            $newinvoince->status = "Paid";
        } else {
            $newinvoince->status = "Unpaid";
        }
        $newinvoince->invoice_date = FrozenTime::now();
        $newinvoince->due_date = FrozenTime::now()->modify('+15 days');
        if ($invoiceTable->save($newinvoince)) {
            $newinvoince->invoice_number = 'INV-' . str_pad((string)$newinvoince->id, 5, '0', STR_PAD_LEFT);
            $invoiceTable->save($newinvoince);
          //  debug($invoice);
        } else {
            debug("Failed to save invoice");
        }
        debug($totalCost);
    }

    function getn_inovice_number(){
        

    }

    

    
 


}
