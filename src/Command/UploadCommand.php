<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Helper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Cake\ORM\TableRegistry;
/**
 * Upload command.
 */
class UploadCommand extends Command { 

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
                    'fileid' => [
                        'short' => 'i',
                        'help' => 'The upload ID of the from uploads table',
                        'required' => true,
                    ]
                ]
        );

        return $parser;
    }

  
    public function execute(Arguments $args, ConsoleIo $io) {
        print("running with arg\n");
        // debug($args);
        $file_id = $args->getOption('fileid');
        $this->_imporfromexcel($file_id);
    }

    function _imporfromexcel($id) {
        $info = $this->getTableLocator()->get('Uploads')->find()->where(['id' => $id])->firstOrFail();
        $postData = (json_decode($info->postdata, true));
        $succcess = 0;
      //  debug($postData);
        
        if (isset($postData['contact_id'])) {
            $contact_group = $postData['contact_id'];
            $tmpName = $postData['fname'];
            unset($postData['fname']);
            unset($postData['contact_id']);
            $mapping = array_flip(($postData));
            if (!isset($mapping['mobile_number'])) {
                $result['msg'] = "Please select Mobile Number field ";
                $result['status'] = "failed";
            } else {
                $helper = new Helper\Sample();
                $spreadsheet = IOFactory::load($tmpName);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                $total_record=count($sheetData);
                $highestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
                $i=0;
             //   $table=$this->getTableLocator()->get('Upload');
                foreach ($sheetData as $key => $val) {
                    $row = [];
                    foreach ($mapping as $mkey => $mval) {
                        if (!empty($mkey)) {
                            $row[$mkey] = $val[$mval];
                        }
                    }
                    $result = $this->_savecontactdata($row, $contact_group);
                   $i++;
                   $per=$i/$total_record*100;
                   $this->output($per);
                 //   debug($result);
                    if ($result['status'] == "Success") {
                        $succcess = $succcess + 1;
                    }
                }
                $result['msg'] = "$succcess is uploaded out of $highestRow";
                $result['status'] = "Success";
            }
            unlink($tmpName);
            $this->output("$tmpName has been deleted");
        }
    }
    
    
    function _savecontactdata($data, $groups) {
        if(!isset($data['mobile_number'])){
            $result['status'] = "failed";
            $result['msg'] = "Wrong mobile number ".$data['mobile_number'];
            echo "Wrong Mobile number ". $data['mobile_number'];
            return $result;
        }
        $data['mobile_number'] = $this->_format_mobile($data['mobile_number']);
        $this->output($data['mobile_number']);
        $table = $this->getTableLocator()->get('ContactNumbers');
        $existing = $table->find()
                ->where(['mobile_number' => $data['mobile_number']])
                ->all()
                ->toList()
                ;
        // debug($data);
        if (!empty($existing)) { //updating existing. 
            $this->output("Number available in Global Contact table");
            $id = $existing[0]->id;
            $newrow = $table->get($id);
            $data['id'] = $id;
            //  echo debug ($data);
            $entity = $table->patchEntity($newrow, $data);
            if ($entity->getErrors()) {
                // debug($newrow->getErrors());
                $result['status'] = "failed";
                $result['msg'] = "Validation errors";
                $this->set('result', $result);
                return $result;
            }
            if ($table->save($entity)) {
                $return['status'] = "Success";
                $return['msg'] = $data['mobile_number'] . " has been update in contact list";
                $id = $entity->id;
                $this->updategroupinfo($id, $groups);
            } else {
                $return['status'] = "error";
                $return['msg'] = "Not able update the existing record. try again.";
                debug($newrow->getErrors());
            }
        } else {
             $this->output("Number is new");
            $record = $table->newEntity($data);
            $return = array();
            if ($table->save($record)) {
                $return['status'] = "Success";
                $return['msg'] = $data['mobile_number'] . " has been added to contact list";
                $id = $record->id;
                $this->updategroupinfo($id, $groups);
            } else {
                $return['status'] = "error";
                $return['msg'] = "Not able to save the record. try again.";
            }
        }
        return $return;
    }
    
     function _format_mobile($mobile_number) {
        $country_code = $this->_getsettings('def_isd');
        $len = strlen((string)$mobile_number);
        print ($mobile_number);
        $mobile_number = preg_replace("/^\+/", '', $mobile_number);
        

        if ($len == 10) {
            $mobile_number = $country_code . $mobile_number;
        }
        return $mobile_number;
    }
    
       public function _getsettings($attr = null) {
        if (isset($attr)) {
            $query = TableRegistry::get('Settings')->find();
            $resultsArray = $query
                    ->where(['params' => $attr])
                    ->toArray();
            if (!empty($resultsArray)) {
                return ($resultsArray[0]->value);
            }
        }
    }
    
     function updategroupinfo($id, $groups) {
        $table = $this->getTableLocator()->get('ContactsContactNumbers');
        foreach ($groups as $key => $val) {
            $query = $table->find()
                    ->where(['contact_number_id' => $id, 'contact_id' => $val])
                    ->all()
                    ->toList();
            if (empty($query)) {
                $this->output("Number Not available this group");
                $record = $table->newEmptyEntity();
                $record->contact_number_id = $id;
                $record->contact_id = $val;
                $table->save($record);
            } else {
                 $this->output("Number is already available this group");
            }
        }
    }
    
    
    function output($string){
        print "$string \n";
    }


}
