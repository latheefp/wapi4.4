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
class DashboardsController extends AppController
{

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        //        $this->Security->setConfig('unlockedActions', ['getdata', 'edit']);
    }

    public function isAuthorized($user)
    {
        return true;
    }

    function _getCountinRange($start_date, $end_date, $Table, $conditions = [])
    {
        $this->loadModel($Table);
        $query = $this->$Table->find();
        //      debug("$start_date $end_date " . $this->getMyAccountID());
        if (isset($conditions['account_id'])) {
            //  debug("null");
            unset($conditions['account_id']);
        } else {
            $conditions['account_id'] = $this->getMyAccountID();
        }
        $conditions = [
            'created >=' => $start_date,
            'created <=' => $end_date,
            //  'account_id' => $this->getMyAccountID(),
        ];
        $query->where($conditions);
        // debug($start_date);
        // debug($end_date);
        // debug($query->sql());
        $count = $query->count();
        // debug($count);
        return $count;
    }

    function fetchdataold()
    {
        $result = array();
        $query = $this->request->getQuery();

        $start_date = date('Y-m-d', strtotime($query['start_date']));
        $end_date = date('Y-m-d', strtotime($query['end_date']));
        $this->viewBuilder()->setLayout('ajax');
        $account_id = $this->getMyAccountID();
        $templates = $this->getTableLocator()->get('Templates')->find()->where(['account_id' => $account_id]);
        $result['templates'] = $templates->count();

        $campaigns = $this->getTableLocator()->get('CampaignViews')->find()->where(['account_id' => $account_id]);
        $result['campaigns'] = $campaigns->count();

        $schedules = $this->getTableLocator()->get('ScheduleViews')->find()->where(['account_id' => $account_id]);
        $result['schedules'] = $schedules->count();


        $result['total_msg'] = $this->_getCountinRange($start_date, $end_date, 'Streams', array('select' => 'id'));
        $result['send'] = $this->_getCountinRange($start_date, $end_date, 'Streams', array('type' => 'send'));
        $result['receive'] = $this->_getCountinRange($start_date, $end_date, 'Streams', array('type' => 'receive'));
        $result['rcvq'] = $count = $this->getTableLocator()
            ->get('RcvQueues')
            ->find()
            ->where(['status' => 'queued'])
            ->count();

        $result['sendq'] = $count = $this->getTableLocator()
            ->get('SendQueues')
            ->find()
            ->where(['status' => 'queued'])
            ->count();



        $has_wa = $this->_getCountinRange($start_date, $end_date, 'Streams', array('has_wa' => 1));

        if ($has_wa > 0) {
            $success_rate = $has_wa / $result['send'] * 100;
        } else {
            $success_rate = 0;
        }

        $result['success_rate'] = round($success_rate);

        $result['groups'] = $this->_getCountinRange($start_date, $end_date, 'Contacts', array('processed' => 0, 'account_id' => null));
        $result['contact_numbers'] = $this->getTableLocator()->get('ContactsContactNumbers')
            ->find()
            ->innerJoin(['Contacts' => 'contacts'], [
                'Contacts.id = ContactsContactNumbers.contact_id',
                'Contacts.account_id' => $account_id
            ])
            ->count();
        $balance = $this->getTableLocator()->get('Accounts')->get($account_id);
        $result['balance'] = $balance->current_balance;
        $this->set('result', $result);
    }


    function fetchdata()
    {
         $account_id=$this->getMyAccountID();
        $MetricTable = $this->getTableLocator()->get('Metrics');
        $uniqueModules = $MetricTable
            ->find()
            ->select(['module_name', 'max_id' => $MetricTable->func()->max('id')])
            ->where(['OR' => [['account_id' => $account_id], ['account_id' => 0]]])
            ->group('module_name');



            }

    public
    function index()
    {
        //        $account_id = $this->getMyAccountID();
        //        $templates = $this->getTableLocator()->get('Templates')->find()->where(['account_id' => $account_id]);
        //        $this->set('templates', $templates->count());
        //        $campaigns = $this->getTableLocator()->get('CampaignViews')->find()->where(['account_id' => $account_id]);
        //        $this->set('campaigns', $campaigns->count());
        //        $schedules = $this->getTableLocator()->get('ScheduleViews')->find()->where(['account_id' => $account_id]);
        //        $this->set('schedules', $schedules->count());
        //        $total_msg = $this->getTableLocator()->get('Streams')->find()->where(['account_id' => $account_id]);
        //        $this->set('total_msg', $total_msg->count());
        //        $send = $this->getTableLocator()->get('Streams')->find()->where(['type' => "send", 'account_id' => $account_id])->count();
        //        $this->set('send', $send);
        //        $receive = $this->getTableLocator()->get('Streams')->find()->where(['type' => 'receive', 'account_id' => $account_id])->count();
        //        $this->set('receive', $receive);
        //
        //        $receive = $this->getTableLocator()->get('Streams')->find()->where(['type' => 'receive', 'account_id' => $account_id])->count();
        //        $this->set('receive', $receive);
        //
        //        $rcvq = $this->getTableLocator()->get('RcvQueues')->find()->where(['processed' => 0])->count();
        //        $this->set('rcvq', $rcvq);
        //
        //        $has_wa = $this->getTableLocator()->get('Streams')->find()->where(['has_wa' => 1, 'account_id' => $account_id])->count();
        //        // debug($has_wa);
        //        if ($has_wa > 0) {
        //            $this->set('success_rate', $has_wa / $send * 100);
        //        } else {
        //            $this->set('success_rate', "NA");
        //        }
        //
        //        $this->set('groups', $this->getTableLocator()->get('Contacts')->find()->where(['account_id' => $account_id])->count());
        //        $this->set('contact_numbers', $this->getTableLocator()->get('ContactsContactNumbers')
        //                        ->find()
        //                        ->innerJoin(['Contacts' => 'contacts'], [
        //                            'Contacts.id = ContactsContactNumbers.contact_id',
        //                            'Contacts.account_id' => $account_id
        //                        ])
        //                        ->count());
        //        //  $this->set('balance',$this->Table
        //
        //        $this->set('balance', $this->getTableLocator()->get('Accounts')->get($account_id));
    }

    function getdata(){
        $result=[];
        $account_id = $this->getMyAccountID();
       // debug($account_id);
        $this->viewBuilder()->setLayout('ajax');
        $Accountstable=$this->getTableLocator()->get('Accounts');
        $accountinfo=$Accountstable->get($account_id);
        $result['balance']=$accountinfo->current_balance;

        $metricTable = $this->getTableLocator()->get('Metrics');

        if ($this->getMyGID() == 1) {
            $query  = $metricTable->find()
            ->where(['module_name' => 'SendQueues'])
            ->order(['id' => 'DESC']);
            $result['SendQueues'] = $query->first()->metric_value;;


            $query  = $metricTable->find()
            ->where(['module_name' => 'RcvQueues'])
            ->order(['id' => 'DESC']);
            $RcvQ =
            $result['RcvQueues'] = $query->first()->metric_value;
        }


        $result ['templates'] =$this->_getmetricval('template');
        $result ['Campaings'] =$this->_getmetricval('campaings');
        $result ['contact_numbers'] =$this->_getmetricval('contact_numbers');
        $result ['send'] =$this->_getmetricval('total_send');
        $result ['receive'] =$this->_getmetricval('total_receive');
        $result['total_msg']=$result ['send']+$result ['receive'];
        $result ['success_rate'] =$this->_getmetricval('success_rate');
        $result ['schedules'] =$this->_getmetricval('schdules');
        $result ['rcvq'] =$this->_getmetricval('total_receive');
        $result ['sendq'] =$this->_getmetricval('total_send');
        $result ['groups'] =$this->_getmetricval('groups');
        $result ['success_rate'] =$this->_getmetricval('success_rate');


       $this->set('result',$result);

    }


    function _getmetricval($metric){
        $account_id = $this->getMyAccountID();
        $metricTable = $this->getTableLocator()->get('Metrics');
        $query  = $metricTable->find()
        ->where(['module_name' => $metric, 'account'=>$account_id])
        ->order(['id' => 'DESC']);
        if(!empty($query->first())){
            return $query->first()->metric_value;
        }else{
            return 0;
        }
        

    }



    // function getdata()
    // {
    //     $formData = $this->request->getQuery();
    //     //debug($formData);
    //     $session = $this->request->getSession();
    //     $account_id = $this->getMyAccountID();
    //     $this->viewBuilder()->setLayout('ajax');
    //     //s    $days = 15;

    //     $wa_query = $this->getTableLocator()->get('Streams')->find();
    //     $startDateTime = $formData['start_date'];
    //     $endDateTime = $formData['end_date'];

    //     $createdTime = $wa_query->func()->date_format([
    //         'created' => 'identifier',
    //         "'%d-%m-%Y %H:%i'" => 'literal'
    //     ]);
    //     $wa_query
    //         ->where(
    //             function ($exp) use ($startDateTime, $endDateTime) {
    //                 return $exp->between('Streams.created', $startDateTime, $endDateTime);
    //             }
    //         )
    //         ->andWhere(['account_id' => $account_id])
    //         ->group('Streams.has_wa, timeCreated')
    //         ->order(['created ASC'])
    //         ->having(['has_wa' => true])
    //         ->select([
    //             'timeCreated' => $createdTime,
    //             'has_wa',
    //             'type',
    //             'count' => $wa_query->func()->count('has_wa')
    //         ]);
    //     $this->set('wa_data', $wa_query);

    //     $no_wa_query = $this->getTableLocator()->get('Streams')->find();
    //     $createdTime = $no_wa_query->func()->date_format([
    //         'created' => 'identifier',
    //         "'%d-%m-%Y %H:%i'" => 'literal'
    //     ]);

    //     $no_wa_query->where(
    //         function ($exp) use ($startDateTime, $endDateTime) {
    //             return $exp->between('Streams.created', $startDateTime, $endDateTime);
    //         }
    //     )
    //         ->andWhere(['account_id' => $account_id])
    //         ->group('Streams.has_wa, timeCreated')
    //         ->order(['created ASC'])
    //         ->having(['has_wa' => false])
    //         ->select([
    //             'timeCreated' => $createdTime,
    //             'has_wa',
    //             'type',
    //             'count' => $no_wa_query->func()->count('has_wa')
    //         ]);
    //     //debug($no_wa_query);
    //     $this->set('no_wa_data', $no_wa_query);
    // }

    // function getshedjson()
    // {
    //     $formData = $this->request->getQuery();
    //     //debug($formData);
    //     $session = $this->request->getSession();
    //     $account_id = $this->getMyAccountID();
    //     $this->viewBuilder()->setLayout('ajax');
    //     $startDateTime = $formData['start_date'];
    //     $endDateTime = $formData['end_date'];
    //     $query = $this->getTableLocator()->get('Streams')->find();
    //     $query->where(
    //         function ($exp) use ($startDateTime, $endDateTime) {
    //             return $exp->between('Streams.created', $startDateTime, $endDateTime);
    //         }
    //     )
    //         ->andWhere(['account_id' => $account_id])
    //         ->group('Streams.schedule_id')
    //         ->select([
    //             'schedule_id',
    //             'Schedules.name',
    //             'has_wa',
    //             'count' => $query->func()->count('schedule_id')
    //         ])
    //         ->order(['count DESC'])
    //         ->innerJoinWith('Schedules');
    //     $this->set('data', $query);
    // }
}
