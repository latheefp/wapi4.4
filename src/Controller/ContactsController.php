<?php

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
use Cake\ORM\Query;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Helper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Cake\Http\CallbackStream; // ← 追加

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class ContactsController extends AppController {

    var $uses = array('Campaigns');

    public function isAuthorized($user) {
        return true;
    }

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);
        $formaction = $this->request->getParam('action');

        $this->FormProtection->setConfig('unlockedActions', array(
            $formaction
        ));
    }

    function index() {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('contact_numbers'));
        $this->set('titleforlayout', "ContactNumbers");
    }

    function newcontacts() {
        $this->viewBuilder()->setLayout('ajax');
        $table = $this->getTableLocator()->get('Contacts');
        $result = [];
        if ($this->request->is('post')) {
            $data = $this->request->getData();
//            $session = $this->request->getSession();
            $account_id = $this->getMyAccountID();

            $data['user_id'] = $this->getMyUID();
            $data['account_id'] = $account_id;
         //   debug($data);
            $row = $table->newEntity($data);
            if ($row->getErrors()) {
                $result['status'] = "failed";
                $result['msg'] = "Validation errors";
                //$this->set('result', $result);
                $error = $row->getErrors();
            } else {
                if ($table->save($row)) {
                    $result['status'] = "success";
                    $result['msg'] = "Contact " . $data['name'] . " been added";
                } else {
                    $result['status'] = "failed";
                    $result['msg'] = "Not able to save the the Contact group";
                    $error = $row->getErrors();
                }
            }
        } else {
            $result['status'] = "failed";
            $result['msg'] = "Wrong data Type";
        }
        if (!empty($error)) {
            $result['msg'] = null;
            foreach ($error as $key => $val) {
                foreach ($val as $ekey => $eval) {
                    $result['msg'] = $result['msg'] . " " . $eval;
                }
            }
        }

        $this->set('result', $result);
    }

//     function getmygroups() {
//         $this->viewBuilder()->setLayout('ajax');
// //        $session = $this->request->getSession();
//         $account_id = $this->getMyAccountID();

//         $this->loadModel('Contacts');

//         $query = $this->Contacts->find('all', [
//             'keyField' => 'id',
//             'valueField' => ['name', 'id', 'contact_count'],
//             'conditions' => ['Contacts.account_id' => $account_id],
//         ]);

//         //    $query->order(['ContactNumbersViews.created' => 'DESC']);
//         //   debug($query);
//         $this->set('groups', $query); //table row data
//         // $this->set('groups', $query->all()->toArray()); //table row data
//     }


    function getlist() {
        $this->viewBuilder()->setLayout('ajax');
//        $session = $this->request->getSession();
        $account_id = $this->getMyAccountID();

        $contactTable=$this->getTableLocator()->get('Contacts');

        $query = $contactTable->find()
        ->where(['Contacts.account_id' => $account_id]);
         
        $this->set('data', $this->paginate($query));
    }

    function getcontacts($id = null) {

        $this->viewBuilder()->setLayout('ajax');
        $model = "ContactNumbers";
        $base_table = "contact_numbers";
        $this->viewBuilder()->setLayout('ajax');
        $query = $this->_set_contact_number_query($this->request->getData(), $model, $base_table);
        $this->set('data', $this->paginate($query));
        $this->set('fieldsType', $this->_fieldtypes($base_table));
    }

    function _set_contact_number_query($querydata, $model, $base_table) {
       //    debug($querydata);
        if (isset($querydata['length'])) {
            $limit = intval($querydata['length']);
        } else {
            $limit = $this->_getsettings('pagination_count');
        }
        $query = array();
        $table = $this->getTableLocator()->get($model);
        $query = $table
                ->find()
                ->limit($limit)
        ;

        $fields = $this->_fieldtypes($base_table);
        $qarray = [];

      //  debug($fields);

        foreach ($fields as $title => $props) {
            if (($props['viewable'] == true) && ($props['searchable'] == true)) {
                if (isset($querydata['search']['value'])) {
                    $qarray[] = [$model . "." . $props['fld_name'] . ' LIKE' => '%' . $querydata['search']['value'] . '%'];
                }
            }
        }
        //  debug($qarray);
        $query->where([
            'OR' => $qarray
        ]);

        $query->matching('ContactsContactNumbers', function (Query $q) use ($querydata) {
            return $q
                    ->select(['contact_id', 'contact_number_id'])
                    ->where(['ContactsContactNumbers.contact_id' => $querydata['contact_id']]);
        });

        $start = intval($querydata['start']);
        $query->page($start / $limit + 1);
        $query->order($querydata['columns'][$querydata['order']['0']['column']]['name'] . ' ' . $querydata['order']['0']['dir']);
 //        echo debug($query);
        return $query;
    }

    function newcontactnumber() {
        $this->viewBuilder()->setLayout('ajax');
//         if(!$this->_checkallowed('add_group')){
//            $this->redirect("/550");
//        }
//        $session = $this->request->getSession();
//        $account_id = $session->read('Auth.User.account_id');
        $data = $this->request->getData();
        $groups = $data['contact_id'];
        unset($data['contact_id']);
        $return = [];
        if (empty($groups)) {
            $return['status'] = "error";
            $return['msg'] = "Please select the group";
            $this->set('result', $return);
        } else {
            // $table = TableRegistry::get('ContactNumbers');
            $table = $this->getTableLocator()->get('ContactNumbers');
            $existing = $table->find()
                    ->where(['mobile_number' => $data['mobile_number']])
                    ->toList();
            // debug($data);
            if (!empty($existing)) {
                $id = $existing[0]->id;
                $newrow = $table->get($id);
                $data['id'] = $id;
                //  echo debug ($data);
                $entity = $table->patchEntity($newrow, $data);
                if ($entity->getErrors()) {
                    //     debug($newrow->getErrors());
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
                    //debug($newrow->getErrors());
                }
            } else {
//                debug($data);
                //   $data['account_id']=$account_id;
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
        }


        $this->set('result', $return);
    }

    function updategroupinfo($id, $groups) {
        $table = $this->getTableLocator()->get('ContactsContactNumbers');
        //    $table->deleteAll(['contact_number_id' => $id]);

        foreach ($groups as $key => $val) {
            $query = $table->find()
                    ->where(['contact_number_id' => $id, 'contact_id' => $val])
                    ->toList();
            if (empty($query)) {
                $record = $table->newEmptyEntity();
                $record->contact_number_id = $id;
                $record->contact_id = $val;
                $table->save($record);
            } else {
                //  debug("Duplicate $id on $val");
            }
        }
    }

    function newcontactupload() {
        //https://www.webscodex.com/2020/10/file-upload-in-cakephp-4.html

        $this->viewBuilder()->setLayout('ajax');
        $postData = $this->request->getData();
        $result = [];
        $data = [];
        if (!isset($postData['file'])) {
            $result['status'] = "failed";
            $result['msg'] = "No attachment file";
        } else {
            $tmpName = $_FILES['file']['tmp_name'][0];
            // debug($tmpName);

            $newfname = ROOT . DS . "tmp/" . $this->_genrand(6);
            //   debug($newfname);

            copy($tmpName, $newfname);

            $helper = new Helper\Sample();
            $spreadsheet = IOFactory::load($newfname);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            //$highestColumn = $spreadsheet->setActiveSheetIndex(0)->getHighestColumn();  
            $highestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
            if ($highestRow > 10) {
                $maxrowshow = 10;
            } else {
                $maxrowshow = $highestRow;
            }
            if (!empty($sheetData)) {
                $colLength = count($sheetData[1]);
                //debug($colLength);
                $result['colLength'] = $colLength;
                $result['data'] = [];
            }
            $data = [];

            foreach ($sheetData as $row => $val) {
                //         debug($val);
                foreach ($val as $colkey => $colval) {
                    $data[$colkey][] = $colval;
                }
                $maxrowshow = $maxrowshow - 1;
                if ($maxrowshow == 0) {
                    break;
                }
            }
            $result['data'] = $data;
            $result['fname'] = $newfname;
        }

        $this->set('result', $result);
    }

    function imporfromexcel() {
        $this->viewBuilder()->setLayout('ajax');
        $postData = $this->request->getData();
        $succcess = 0;
        if (isset($postData['contact_id'])) {
            $table = $this->getTableLocator()->get('Uploads');
            $row = $table->newEmptyEntity();
            $row->postdata = json_encode($postData);
            $row->user_id = $this->getMyUID();
            $table->save($row);
            $cmd = ROOT . DS . "bin/cake upload -i $row->id > /dev/null 2>&1 &";
            exec($cmd);
            $result['msg'] = "The job is submited with a job id $row->id, please check the upload status";
            $result['status'] = "success";
        } else {
            $result['msg'] = "Please select the groups";
            $result['status'] = "failed";
        }

        $this->set('result', $result);
    }

    function imporfromexcelold() {
        $this->viewBuilder()->setLayout('ajax');
        $postData = $this->request->getData();
        $succcess = 0;
        // debug($postData);
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
                $highestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
                foreach ($sheetData as $key => $val) {
                    $row = [];
                    foreach ($mapping as $mkey => $mval) {
                        if (!empty($mkey)) {
                            $row[$mkey] = $val[$mval];
                        }
                    }
                    $result = $this->_savecontactdata($row, $contact_group);
                    // debug($result);
                    if ($result['status'] == "Success") {
                        $succcess = $succcess + 1;
                    }
                }
                $result['msg'] = "$succcess is uploaded out of $highestRow";
                $result['status'] = "Success";
            }
        } else {
            $result['msg'] = "Please select the groups";
            $result['status'] = "failed";
        }

        $this->set('result', $result);
    }

    function _savecontactdata($data, $groups) {
        $data['mobile_number'] = $this->_format_mobile($data['mobile_number']);
        $table = $this->getTableLocator()->get('ContactNumbers');
        $existing = $table->find()
                ->where(['mobile_number' => $data['mobile_number']])
                ->toList();
        // debug($data);
        if (!empty($existing)) { //updating existing. 
            // debug($existing);
            //   debug ("updating record");
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
                //debug($newrow->getErrors());
            }
        } else {
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

    function checkwhatsapp() {
        
    }

    function deletecontact($id) {
        $result = [];
        $this->viewBuilder()->setLayout('ajax');
        $tablecontacts = $this->getTableLocator()->get('Contacts'); //the group table

        $deleterecord = $tablecontacts->find()
                ->where(['account_id'=>$this->getMyAccountID(),'id'=>$id]);

        $recordCount = $deleterecord->count(); 

        if ($recordCount > 0) {
            foreach ($deleterecord as $record) {
                $tablecontacts->delete($record);
            }
        
            $result['status'] = "success";
            $result['msg'] = "Group has been deleted.";
        } else {
            $result['status'] = "error";
            $result['msg'] = "No records found to delete.";
        }
        
        $this->set('result', $result);
    }

    function test() {
        // $this->viewBuilder()->setLayout('ajax');
        $token = "88975478KLU96C32";
        $outputHtml = null;
        $table = $this->getTableLocator()->get('RefLists');

        $query = $table->find()
                ->where(['token' => $token])
                ->first();

        //  debug ($query);
//        
//        $model=$query->table_name;
//        $field_name=$query->field_name;
//        
//        $outputHtml='<select id="'.$token.'" class="form-control" ><option value="0">- Search Here -</option></select>';
//              
//        
        //     debug($outputHtml);
    }

}
