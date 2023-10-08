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

//use App\Controller\Query;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class DashboardsController extends AppController {

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);
//        $this->Security->setConfig('unlockedActions', ['getdata', 'edit']);
    }

    public function isAuthorized($user) {
        return true;
    }

    function _getCountinRange($start_date, $end_date, $Table, $conditions = []) {
        $this->loadModel($Table);
        $query = $this->$Table->find();
  //      debug("$start_date $end_date " . $this->getMyAccountID());
        if (isset($conditions['account_id'])) {
            debug("null");
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
      //  debug($query->sql());
        $count = $query->count();
        return $count;
    }

    function fetchdata() {
        $result = array();
        $query = $this->request->getQuery();
//        $start_date = $query['start_date'];
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

        //$total_msg = $this->getTableLocator()->get('Streams')->find()->where(['account_id' => $account_id]);
        $result['total_msg'] = $this->_getCountinRange($start_date, $end_date, 'Streams');
        // debug($result['total_msg']);
        //    debug($q->count());
        //  $result['total_msg'] = $total_msg->count();
        $result['send'] = $this->_getCountinRange($start_date, $end_date, 'Streams', array('type' => 'send'));
//        $send = $this->getTableLocator()->get('Streams')->find()->where(['type' => "send", 'account_id' => $account_id])->count();
//        $result['send'] = $send;
        //   $receive = $this->getTableLocator()->get('Streams')->find()->where(['type' => 'receive', 'account_id' => $account_id])->count();
        $result['receive'] = $this->_getCountinRange($start_date, $end_date, 'Streams', array('type' => 'receive'));
//        $rcvq = $this->getTableLocator()->get('RcvQueues')->find()->where(['processed' => 0])->count();
        $result['rcvq'] = $this->_getCountinRange($start_date, $end_date, 'RcvQueues', array('processed' => 0, 'account_id' => null));
        ;

//        $has_wa = $this->getTableLocator()->get('Streams')->find()->where(['has_wa' => 1, 'account_id' => $account_id])->count();
        $has_wa = $this->_getCountinRange($start_date, $end_date, 'Streams', array('has_wa' => 1));
        ;
        if ($has_wa > 0) {
            $success_rate = $has_wa / $result['send'] * 100;
        } else {
            $success_rate = "NA";
        }
        $result['success_rate'] = round($success_rate);
//      $result['groups'] = $this->getTableLocator()->get('Contacts')->find()->where(['account_id' => $account_id])->count();
        $result['groups'] = $this->_getCountinRange($start_date, $end_date, 'Contacts', array('processed' => 0, 'account_id' => null));
        ;
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

    public
            function index() {
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

    function getdata() {
        $formData = $this->request->getQuery();
        //debug($formData);
        $session = $this->request->getSession();
        $account_id = $this->getMyAccountID();
        $this->viewBuilder()->setLayout('ajax');
        //s    $days = 15;

        $wa_query = $this->getTableLocator()->get('Streams')->find();
        $startDateTime = $formData['start_date'];
        $endDateTime = $formData['end_date'];

        $createdTime = $wa_query->func()->date_format([
            'created' => 'identifier',
            "'%d-%m-%Y %H:%i'" => 'literal'
        ]);
        $wa_query
                ->where(
                        function ($exp) use ($startDateTime, $endDateTime) {
                            return $exp->between('Streams.created', $startDateTime, $endDateTime);
                        }
                )
                ->andWhere(['account_id' => $account_id])
                ->group('Streams.has_wa, timeCreated')
                ->order(['created ASC'])
                ->having(['has_wa' => true])
                ->select([
                    'timeCreated' => $createdTime,
                    'has_wa',
                    'type',
                    'count' => $wa_query->func()->count('has_wa')
        ]);
        $this->set('wa_data', $wa_query);

        $no_wa_query = $this->getTableLocator()->get('Streams')->find();
        $createdTime = $no_wa_query->func()->date_format([
            'created' => 'identifier',
            "'%d-%m-%Y %H:%i'" => 'literal'
        ]);

        $no_wa_query->where(
                        function ($exp) use ($startDateTime, $endDateTime) {
                            return $exp->between('Streams.created', $startDateTime, $endDateTime);
                        }
                )
                ->andWhere(['account_id' => $account_id])
                ->group('Streams.has_wa, timeCreated')
                ->order(['created ASC'])
                ->having(['has_wa' => false])
                ->select([
                    'timeCreated' => $createdTime,
                    'has_wa',
                    'type',
                    'count' => $no_wa_query->func()->count('has_wa')
        ]);
//debug($no_wa_query);
        $this->set('no_wa_data', $no_wa_query);
    }

    function getshedjson() {
        $formData = $this->request->getQuery();
        //debug($formData);
        $session = $this->request->getSession();
        $account_id = $this->getMyAccountID();
        $this->viewBuilder()->setLayout('ajax');
        $startDateTime = $formData['start_date'];
        $endDateTime = $formData['end_date'];
        $query = $this->getTableLocator()->get('Streams')->find();
        $query->where(
                        function ($exp) use ($startDateTime, $endDateTime) {
                            return $exp->between('Streams.created', $startDateTime, $endDateTime);
                        }
                )
                ->andWhere(['account_id' => $account_id])
                ->group('Streams.schedule_id')
                ->select([
                    'schedule_id',
                    'Schedules.name',
                    'has_wa',
                    'count' => $query->func()->count('schedule_id')
                ])
                ->order(['count DESC'])
                ->innerJoinWith('Schedules')
        ;
        $this->set('data', $query);
    }
}
