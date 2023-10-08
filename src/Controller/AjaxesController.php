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
use Cake\Core\Configure;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class AjaxesController extends AppController {

    public function initialize(): void {
        parent::initialize();

        $this->loadComponent('FormProtection');
    }

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);
//        $this->Security->setConfig('unlockedActions', ['getdata', 'edit']);
//        $this->FormProtection->setConfig('validate', false);
//        if ($this->request->getParam('controller') === 'Ajaxes') {
//            $this->FormProtection->setConfig('validate', false);
//        }
//        $this->Auth->allow(['getrate']);
    }

    public function isAuthorized($user) {
        return true;
    }

    public function index() {
        
    }

//    function getlist1($table = null, $string = null) {
//        //  debug ($query);
//        $this->viewBuilder()->setLayout('ajax');
//        $query = TableRegistry::get($table)->find();
//        $resultsArray = $query
//                ->where(['name LIKE' => "%" . $string . "%"])
//                ->toArray();
//        $this->set('result', $resultsArray);
//    }

    function getlist($token = null, $string = null) {
        $this->viewBuilder()->setLayout('ajax');
        $reftable = TableRegistry::get('RefLists')->find();
        $resultsArray = $reftable
                ->where(['token' => $token])
//   ->toArray()
                ->first();

        $table = $resultsArray->table_name;
//        
        debug($resultsArray);
//        
        $function = $resultsArray->function;
        $field_name = $resultsArray->field_name;
        $query = TableRegistry::get($table)->find();
        switch ($function) {
            case "default":
                $jsons = array();
                if (($resultsArray->account) == 1) {  //if account ID also included in query. 
                    $company = $this->request->getSession()->read('Account.id');
                    $resultsArray = $query
                            ->where([$field_name . ' LIKE' => "%" . $string . "%", 'company_id' => $company])
                            ->toArray();
                } else {
                    $resultsArray = $query
                            ->where([$field_name . ' LIKE' => "%" . $string . "%"])
                            ->toArray();
                }
                foreach ($resultsArray as $key => $val) {
                    $json['id'] = $val->id;
                    $json['name'] = $val->$field_name;
                    $jsons[] = $json;
                }
                break;
        }
        $this->set('result', $jsons);
    }

//    public function _getsettings($attr = null) {
//        if (isset($attr)) {
//            $query = TableRegistry::get('Settings')->find();
//            $resultsArray = $query
//                    ->where(['parameter' => $attr])
//                    ->toArray();
//            if (!empty($resultsArray)) {
//                return ($resultsArray[0]->value);
//            }
//        }
//    }
//     function validate1($table=null,$type=null){
//            $this->viewBuilder()->setLayout('ajax');
//            $result=$this->_ajaxvalidation($table, $this->request->getData(),$type);
//          //  debug ($result);
//            $this->set('result',$result);
//        }


    function validate($Table, $type) {
        $result = [];
        $data = $this->request->getData();
        $data['company_id'] = $this->request->getSession()->read('Company.id');
// debug($data);
        $this->loadModel($Table);
        $table = TableRegistry::get($Table);
        if ($type == "edit") {
            $id = $data['id'];
// debug ($type);
            $newrow = $table->get($data['id']);
            $table->patchEntity($newrow, $data);
        } else {
            $newrow = $table->newEmptyEntity();
            $newrow = $table->patchEntity($newrow, $data);
        }
//  debug($newrow);
        $errors = $newrow->getErrors();
        if (empty($errors)) {
            $this->set('result', $errors);
        } else {
            foreach ($errors as $key => $val) {
                $error['field'] = $key;

                foreach ($val as $msgkey => $msgval) {
                    $error['error'] = $msgval;
                }
                $result[] = $error;
                $error = array();
            }
            $this->set('result', $result);
        }
    }

    function gettimeout() {
        $this->viewBuilder()->setLayout('ajax');
        print (Configure::read('Session.timeout'));
    }

    function getproduct() {
        $this->viewBuilder()->setLayout('ajax');
        $ProductView = TableRegistry::get('ProductsViews');
        $data = $this->request->getQuery();
        $formdata = $data['barcode'];
//debug ($formdata);
        $query = $ProductView->find()
                ->where([
                    'OR' => [
                        ['product_name LIKE' => '%' . $formdata . '%'],
                        ['barcode' => $formdata]
                    ]
                        ]
                )
                ->toArray();
        $this->set('products', $query);
    }

    function getcustomer() {
        $this->viewBuilder()->setLayout('ajax');
        $CustomerTable = TableRegistry::get('Customers');
        $data = $this->request->getQuery();
        $formdata = $data['barcode'];
//debug ($formdata);
        $query = $CustomerTable->find()
                ->where([
                    'OR' => [
                        ['customer_name LIKE' => '%' . $formdata . '%'],
                        ['phone' => $formdata]
                    ]
                        ]
                )
                ->toArray();
        $this->set('products', $query);
    }

    function getselect2data($token = null) {
        $data = [];

        $table = $this->getTableLocator()->get('RefLists');

        $query = $table->find()
                ->where(['token' => $token])
                ->first();

        $model = $query->table_name;
        $field = $query->field_name;
        if (isset($model)) {
            $table = $this->getTableLocator()->get($model);
            $searchTerm = $this->request->getData();
            $searchTerm = $searchTerm['searchTerm'];
            $query = $table->find()
                    ->where([$field . ' LIKE' => '%%' . $searchTerm . '%%'])
                    ->limit(10);

            foreach ($query as $key => $val) {
                $data[] = array("id" => $val->id, "text" => $val->$field);
            }
        }
        $this->set('data', $data);
    }

    function getrate() {
        $result = [];
        $this->viewBuilder()->setLayout('ajax');
        $rateTable = TableRegistry::get('PriceCards');
        $data = $this->request->getData();
// $formdata = $data['barcode'];
//    debug($data);
        $query = $rateTable
                ->find()
                ->where(
                        [
                            'id' => $data['country']
                        ]
                )
                ->first();
        $msg_type = $data['message_type'];
// debug($query);

        $rate = $query->$msg_type;
        if (is_numeric($rate)) {
            $profit_perc = $this->_getsettings('profit_margin');

            $tax_perc = $this->_getsettings('tax');
            $rate_with_tax = ($rate * ($tax_perc / 100)) + $rate;

            $customer_rate_single = ($rate_with_tax * ($profit_perc / 100)) + $rate_with_tax;

            $result['status'] = "success";
            $result['msg'] = round(($customer_rate_single * $data['numbers']));
        } else {
            $result['status'] = "failed";
            $result['msg'] = "Wrong data";
        }

        $this->set('result', $result);
    }

    function contact() {
        $this->viewBuilder()->setLayout('ajax');
        $data = $this->request->getData();
        $ContactformTable = $this->getTableLocator()->get('ContactForms');
        $row = $ContactformTable->newEmptyEntity();
        $$row = $ContactformTable->patchEntity($row, $data); 
        if($ContactformTable->save($row)){
            $result['status']="success";
            $result['msg']="Thank you for your contacting us, we will reach you soon back";
        }else{
            $error=$row->getErrors();
           // debug($error);
            $msg=null;
            foreach($error as $key =>$valarray){
                foreach ($valarray as $valkey=>$valmsg){
                    $msg=$msg."<b>".$key."</b>".":".$valmsg."<br>";
                }
            }
            $result['status']="failed";
            $result['msg']="Failed to save data, $msg";
        }
        
        $this->set('result',$result);
    }

}
