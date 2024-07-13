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

class ProcesssendqCommand extends Command {

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
        $parser = parent::buildOptionParser($parser);

   

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io) {

        while (true) {
            if (intval(getenv('SNDQRUN')) == true) {
                 $io->out("SNDQRUN Enabled, processing");
                $this->process_sendq();
            }else{
                $io->out("SNDQRUN is disabled,  waiting 300 seconds");
                sleep (300);
            }
        }


        // if (intval(getenv('SNDQRUN')) == true) {
        //     print "SNDQRUN Enabled, processing";
        //     $this->process_sendq();
        // }else{
        //     debug("SND Q is Disabled by settings");
        //   //  exit(0);
        // }
           

        print("running with arg\n");
        ;
    }

    public function initialize(): void {
        parent::initialize();
        $this->app = new AppController();
    }

    function process_sendq() {
        $apiKey = 'sm4UFJUHdHi8HXlrqQx2uqUbek4w6ZdlcGmS0enGTFI0pAbIV6EFk6QwtghSOlRh';
        $table = TableRegistry::getTableLocator()->get('SendQueues');
        while (true) {
//            print ".";
            $queued = $query = $table->find()
                    ->where([
                        'status' => 'queued',
                    ])
                    ->all();

            foreach ($queued as $key => $val) {
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
                    $stmt = $connection->execute('UPDATE send_queues SET status = ? , process_start_time= ?  WHERE id = ? AND status = ?', ["processing", $mysqlFormattedTimestamp, $val->id, 'queued']);
                    // debug($stmt);
                    $affectedRows = $stmt->rowCount();
                    if ($connection->commit()) {
                        if ($affectedRows > 0) {
                            debug("Transaction committed successfully. {$affectedRows} rows were affected.");
                            $maxParallelProcesses = $this->app->_getsettings('max_parallel_que_processing');
                            $cmd = ROOT . '/bin/runsendprocess.pl  -i ' . $val->id . ' -k ' . $apiKey . ' >' . ROOT . '/logs/process.log 2>&1 &';
                          //  $cmd = ROOT . '/bin/runsendprocess.pl  -i ' . $val->id . ' -k ' . $apiKey ;
                            debug($cmd);
                            usleep(100);
                            exec($cmd);
                        } else {
                         //   debug("Transaction committed, but no rows were affected.");
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
            sleep(2);
        }
    }

    function checklimit() {
        $maxParallelProcesses = $this->app->_getsettings('max_parallel_que_processing');
        $table = TableRegistry::getTableLocator()->get('RcvQueues');
        #    date('Y-m-d H:i:s');
        $query = TableRegistry::getTableLocator()->get('SendQueues')
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


    function cleanq(){
        
    }


}
