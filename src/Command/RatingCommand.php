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
use App\Controller\AppController; //(path to your controller).


/**
 * Upload command.
 */
class RatingCommand extends Command {

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
                    'batch' => [
                        'short' => 'b',
                        'help' => 'Batch with -b',
                        'required' => true,
                    ],
                    'batchsize' => [
                        'short' => 's',
                        'help' => 'Batch siz with -s',
                        'required' => true,
                    ]
                ]
        );

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        print("running with arg\n");
        // debug($args);
        $batch = $args->getOption('batch');
        $batchsize = $args->getOption('batchsize');
        $this->updatebalance($batch,$batchsize);
    }

    public function initialize(): void {
        parent::initialize();
        $this->app = new AppController();
    }

    function updatebalance($batch_id, $batchSize) {
        $updated = array();
      //  $batchSize = $batchsize;
        $StreamsTable = $this->getTableLocator()->get('Streams');
        $totalRecords = $StreamsTable->find()
                ->where(function ($exp, $q) {
                    return $exp->isNotNull('tmp_upate_json');
                })
                ->andWhere(['rated' => false, 'type' => "send"])
                ->select(['tmp_upate_json', 'id'])
                ->count();

        debug("Total Record is : $totalRecords");

        $numBatches = ceil($totalRecords / $batchSize);

        for ($batch = $batch_id; $batch <= $numBatches; $batch++) {
            debug("Batch: $batch");
            $offset = ($batch - 1) * $batchSize;

            $rows = $StreamsTable->find()
                    ->where(function ($exp, $q) {
                        return $exp->isNotNull('tmp_upate_json');
                    })
                    ->andWhere(['rated' => false, 'type' => "send"])
                    ->select(['tmp_upate_json', 'id'])
                    ->limit($batchSize)
                    ->offset($offset)
                  //  ->order('conversationid ASC')
                    ->all();

            // Process the $rows here, for example:
            foreach ($rows as $key => $val) {
                // debug($val->id);
                $data = trim($val->tmp_upate_json, ',');
                //     debug($data);
                $jsonArray = explode("\n", $data);
                foreach ($jsonArray as $jkey => $jval) {
                    if (!empty($jval)) {
                        //    debug($jval);
                        $jval = trim($jval, ',');
                        $status = json_decode($jval, true);

                        if (isset($status['pricing'])) {

                            if (isset($status['conversation'])) { //This is code is not tested. added to avoid bug related to emptry coversation ID in _rateMe();
                                $ratingquery = $this->getTableLocator()->get('Ratings')->find();
                                $ratingquery->where([
                                            ['conversation' => $status['conversation']['id']]
                                        ])
                                        ->first();
                                //Billing is needed only for Uniq conversation IDS.
                                //ALTER TABLE `ratings` ADD `conversation` VARCHAR(64) NULL DEFAULT NULL AFTER `fb_cost`; 
                                //ALTER TABLE `streams` ADD `rated` BOOLEAN NOT NULL DEFAULT FALSE AFTER `cost`; 
                                if (empty($ratingquery)) {
                                    debug("Rating " . $status['conversation']['id']);
                                    $this->app->_rateMe($status);
                                } else {
                                    // debug($ratingquery);
                                    
                                    if (!isset($updated[$status['conversation']['id']])) {
                                        debug("Already Rated updating all fields of $val->id :" . $status['conversation']['id']);
                                        $streamsTable = $this->getTableLocator()->get('Streams');
                                        $streamsTable->updateAll(
                                                ['rated' => true],
                                                ['conversationid' => $status['conversation']['id']]
                                        );
                                        $updated[$status['conversation']['id']] = true;
                                       // debug ("Updated all rated.");
                                     //   debug($updated);
                                    }else{
                                        debug ("Already updated $val->id");
                                    }
                                }
                            }
                        } //if isset pricing end here. 
                    }
                }
            }
        }
    }

}
