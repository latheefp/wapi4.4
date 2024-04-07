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
class ContactArchiveCommand extends Command { 

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

    function archive(){
        //getlist of all contacts_numbers
        //if contact number is not part of any active streams and not part of any contacts(groups), it can be deleted.

        $result = [];
        $this->viewBuilder()->setLayout('ajax');
        $tablecontacts = $this->getTableLocator()->get('Contacts'); //the group table
        $tablecontactContactnumber = $this->getTableLocator()->get('ContactsContactNumbers'); // the relation of contact group and mobile numbers. 
        $ccnquery = $tablecontactContactnumber->find();
        $ccnquery->where(['contact_id' => $id]) //getting all related to number_ids
                ->toArray();

        foreach ($ccnquery as $ccnkey => $ccnval) {
//            debug("contact number entries relaed to $id");
//            debug($ccnval);
            $contact_number_id = $ccnval->contact_number_id;
            $ccountquery = $tablecontactContactnumber->find();

            //get all related records in ccn array for $id with count
            $ccountquery->select(['contact_id', 'contact_number_id', 'id', 'count' => $ccountquery->func()->count('ContactsContactNumbers.contact_number_id')])
                    ->where(['contact_number_id' => $contact_number_id])
                    ->group(['contact_number_id'])
                    ->toArray();

            //   debug($ccountquery);
            foreach ($ccountquery as $countkey => $countval) {
                $count = $countval->count;
                // debug($countval);
                //   debug("contact number id: $contact_number_id has $count match");
                #if only one match, delete both number and ccn else delete only ccn.
                if ($count == 1) {
                    $deleterecod = $tablecontactContactnumber->get($ccnval->id);
                    $tablecontactContactnumber->delete($deleterecod);
                    $contact_numberstable = $this->getTableLocator()->get('ContactNumbers');
                    $deleterecod = $contact_numberstable->get($ccnval->contact_number_id);
                    $contact_numberstable->delete($deleterecod);
                    //   $tablecontactContactnumber->Delete($countval);
                } else {
                    //delete only from ccn table. 
                    $deleterecod = $tablecontactContactnumber->get($ccnval->id);
                    $tablecontactContactnumber->delete($deleterecod);
                }
            }
        }
        $deleterecod = $tablecontacts->get($id);
        $tablecontacts->delete($deleterecod);
        $result['status'] = "success";
        $result['msg'] = "Group has been deleted.";
        $this->set('result', $result);
    }


}
