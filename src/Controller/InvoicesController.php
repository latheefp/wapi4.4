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
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;

//use App\Controller\Query;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class InvoicesController extends AppController
{

    public function beforeFilter(EventInterface $event): void
    {
 
        //        $this->Security->setConfig('unlockedActions', ['getdata', 'edit']);
       
            parent::beforeFilter($event);
            $this->FormProtection->setConfig('unlockedActions', ['getdata']);
        
    }

    public function isAuthorized($user)
    {
        return true;
    }

    function index() {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('invoice_views'));
       
        $this->set('titleforlayout', "List Invoices");
        $this->set('account_id',$this->getMyAccountID());
    }

    public function getdata() {
        $model = "InvoiceViews";
        $base_table = "invoice_views";
        $this->viewBuilder()->setLayout('ajax');
//   debug($this->request->getData());
        $query = $this->_set_invoice_query($this->request->getData(), $model, $base_table);
        $data = $this->paginate = $query;
        $this->set('data', $this->paginate($model));
    //    debug($this->_fieldtypes($base_table));
        $this->set('fieldsType', $this->_fieldtypes($base_table));
    }

    public function _set_invoice_query($querydata, $model, $base_table) {  //return array of quey based on passed values from index page search.
       // debug($querydata);
        $query = [
            'order' => [
                $model . '.id' => 'desc'
            ]
        ];
        if (isset($querydata['length'])) {
            $query['limit'] = intval($querydata['length']);
        } else {
            $query['limit'] = $this->_getsettings('pagination_count');
        }
        $fields = $this->_fieldtypes($base_table);
//  debug($fields);
        foreach ($fields as $title => $props) {
            if (($props['viewable'] == true) && ($props['searchable'] == true)) {
                if (isset($querydata['search']['value'])) {
                    $query['conditions']['OR'][] = array($model . "." . $props['fld_name'] . ' LIKE' => '%' . $querydata['search']['value'] . '%');
                }
            }
        }
//        $session = $this->request->getSession();
        $query['conditions']['AND'][] = array($model . ".account_id" =>$this->getMyAccountID());

        $start = intval($querydata['start']);
        $query['page'] = ($start / $query['limit']) + 1;
        $query['order'] = array($querydata['columns'][$querydata['order']['0']['column']]['name'] . ' ' . $querydata['order']['0']['dir']);
//  debug($query);
        return $query;
    }

    function details($invoice_id){
        $ratingsTable=$this->getTableLocator()->get('Ratings');
        $matchingRecords = $ratingsTable
            ->find()
            ->where([
                'MONTH(Ratings.created)' => $month,
                'YEAR(Ratings.created)' => $year,
                'Streams.account_id' => $account_id
            ])
            ->contain(['Streams']);

    }

    

   
}
