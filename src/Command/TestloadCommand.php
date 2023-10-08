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
class TestloadCommand extends Command {

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
//                    'batch' => [
//                        'short' => 'b',
//                        'help' => 'Batch with -b',
//                        'required' => true,
//                    ],
//                    'batchsize' => [
//                        'short' => 's',
//                        'help' => 'Batch siz with -s',
//                        'required' => true,
//                    ]
                ]
        );

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        print("running with arg\n");
        // debug($args);
       $this->looppost();
    }

    public function initialize(): void {
        parent::initialize();
        
    }
    
    function looppost(){
         $table = $this->getTableLocator()->get('Queues');
         $query=$table->find()
                 ->all();
         foreach($query as $key =>$val){
             debug($val->id);
             $this->postdata($val->json);
         }
    }

    function postdata($json) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost:80/apis/webhook',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
}
