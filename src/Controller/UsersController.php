<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Hash;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use Cake\Event\Event;
use DateTime;
use Cake\Mailer\Mailer;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class UsersController extends AppController {

    var $uses = array('Users');

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login', 'logout']);
//        $this->Security->setConfig('unlockedActions', ['getdata', 'edit']);
    }

    public function listusers() {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('Users'));
    }

    public function landingPage() {
        
    }

    public function forgetpass() {
        $this->viewBuilder()->setLayout('login');
        $loggeduser = $this->getRequest()->getSession()->read('Auth.User');
        if (isset($loggeduser)) {
            return $this->redirect($this->Auth->redirectUrl());
        }
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $info = TableRegistry::get('Users')
                    ->find()
                    ->where($data)
                    ->first();
            if (isset($info)) {
                $link = $this->_setpasslink($info->id);
                if ($link) {
                    $result['msg'] = "Password reset link has been email to you ";
                    $result['status'] = "success";
                    $result['link'] = $link;
                } else {
                    $result['msg'] = "Cannot reset your password. Please try latter";
                    $result['status'] = "failed";
                }
            } else {
                $result['msg'] = "Wrong email/phone number combinations provided";
                $result['status'] = "failed";
                $result['link'] = null;
            }
            //           debug ($result);
            $this->set('result', $result);
//            
        }
    }

    function _setpasslink($user_id) {
        $current_time = new DateTime();
        //echo $current_time->format('Y-m-d H:i:s');
        $Usertable = TableRegistry::get("Users");
        $user = $Usertable->get($user_id);
        $user['password_link'] = $this->gen_rand_string(32);
        $user['link_created'] = $current_time->format('Y-m-d H:i:s');
        $url = $this->_getsettings('url');
        $link = $url . "resetpass/" . $user['password_link'];
        if ($Usertable->save($user)) {
            $this->_email_passlink($user->email, $link);
            return $link;
        } else {
            return false;
        }
    }

    function _email_passlink($email, $link) {
        $mailer = new Mailer('default');
        $mailfrom = $this->_getsettings('mailfrom');
        $application = $this->_getsettings('application_name');
        $application_version = $this->_getsettings('application_version');
        $mailfrom_name = $this->_getsettings('mailfrom_name');
        $mailer->setFrom([$mailfrom => $mailfrom_name])
                ->setTo($email)
                ->setSubject('Your password for ' . $application . " " . $application_version)
                ->viewBuilder()
                ->setTemplate('resetpass')
                ->setLayout('fancy');
        $mailer->deliver('Please click below link for password reset \n.' . $link);
    }

//    function resetpass(){
//        
//    }

    function resetpass($token = null) {
        $this->viewBuilder()->setLayout('login');
        $result = array();
        $user = array();
        //$query=$this->request->getQuery();
        $loggeduser = $this->getRequest()->getSession()->read('Auth.User');
        //  debug($token);
        if (isset($loggeduser)) {
            return $this->redirect($this->Auth->redirectUrl());
        }
        $expiry_minutes = $this->_getsettings('pass_link_expiry_minutes');
        //  debug($expiry_minutes);
        //  debug ($query['token']);
        if (isset($token)) {

            $info = TableRegistry::get('Users')
                    ->find()
                    ->where(['password_link' => $token])
                    ->first();
            if (isset($info)) {
                //      debug ($info);
                $link_created = $info->link_created;

                $link_expiry = $link_created->modify('+' . $expiry_minutes . ' minutes');
                $current_time = new DateTime();
                if ($current_time <= $link_expiry) {
                    $user = ['id' => $info->id, 'token' => $token, 'email' => $info->email];
                } else {
                    $result['msg'] = "Wrong or expired link";
                    $result['status'] = "failed";
                }
            } else {
                $result['msg'] = "Wrong or expired link";
                $result['status'] = "failed";
            }
        } else {
            return $this->redirect("/login");
        }
        $this->set('user', $user);
        $this->set('result', $result);
    }

    function pubpasswordsetajax() {
        $this->viewBuilder()->setLayout('ajax');
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $expiry_minutes = $this->_getsettings('pass_link_expiry_minutes');
            $info = TableRegistry::get('Users')
                    ->find()
                    ->where([
                        'password_link' => $data['token'],
                        'id' => $data['id'],
                        'email' => $data['email'],
                        'link_created > ' => new DateTime($expiry_minutes . ' minutes ago')
                    ])
                    ->first();
            if (empty($info)) {
                $error['msg'] = "Somethign went wrong";
                $this->set('error', $error);
            } else {
                $Usertable = TableRegistry::get("Users");
                $user = $Usertable->get($data['id']);
                $user->password = $data['password'];
                if ($Usertable->save($user)) {
                    $result['status'] = "Success";
                    $result['msg'] = "Password has been updated";
                } else {
                    $result['status'] = "Failed";
                    $result['msg'] = "Password upate failed";
                }
                $this->set('result', $result);
            }
        }
    }

    public function usermgt() {
        $this->viewBuilder()->setLayout('ajax');
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            //  debug($data);
            switch ($data['action']) {
                case "edit":
                    $result = $this->_dteditvalidation('Users', $data);
                    if (empty($result)) {
                        $id = array_key_first($data['data']);
                        $data = $data['data'][$id];
                        $user = $this->Users->findById($id)->firstOrFail();
                        $this->Users->patchEntity($user, $data);
                        if ($this->Users->save($user)) {
                            if (!isset($data['group_id'])) {
                                $data['group_id'] = [];
                            }
                            $this->_update_group($id, $data['group_id']);
                            $TableUser = TableRegistry::get('Users');
                            $row = $TableUser->find()
                                    ->where(['id' => $id])
                                    ->toArray();
                            $result['data'] = (array) $row;
                            $this->set('fieldsType', $this->_fieldtypes('Users'));
                        }
                    }
                    break;
                case "remove":
                    //   debug ("Action is delete");
                    $this->loadModel('Users');
                    $id = array_key_first($data['data']);
                    $user = $this->Users->findById($id)->firstOrFail();
                    if ($this->Users->delete($user)) {
                        $result = [];
                    } else {
                        $result['error'] = "Cannot delete the data";
                    }
                    break;
                case "create":
                    $result = $this->_dteditvalidation('Users', $data);
                    //          debug ($result);
                    if (empty($result)) {
                        $id = array_key_first($data['data']);
                        $data = $data['data'][$id];
                        $UserTable = TableRegistry::get('Users');
                        $user = $UserTable->newEmptyEntity();
                        $user = $this->Users->patchEntity($user, $data);
                        if ($saveStatus = $this->Users->save($user)) {
                            // debug("Last ID is ". $this->Users->id);
                            $id = $saveStatus->id;
                            if (!isset($data['group_id'])) {
                                $data['group_id'] = [];
                            }
                            $this->_update_group($id, $data['group_id']);
                            $TableUser = TableRegistry::get('Users');
                            $row = $TableUser->find()
                                    ->where(['id' => $id])
                                    ->toArray();
                            $result['data'] = (array) $row;
                            $this->set('fieldsType', $this->_fieldtypes('Users'));
                        } else {
                            $result['error'] = "Cannot create new recored";
                        }
                    }
                    break;
            }

            $this->set('result', $result);
        }
    }

    function setpassword() {
        $data = $this->request->getData();
        $Usertable = TableRegistry::get("Users");
        $user = $Usertable->get($data['user_id']);
        $user->password = $data['password'];
        if ($Usertable->save($user)) {
            $result['status'] = "Success";
            $result['msg'] = "Password has been updated";
        } else {
            $result['status'] = "Failed";
            $result['msg'] = "Password update failed.";
        }
        $this->set('result', $result);
    }

    public function isAuthorized($user) {
        return true;
    }

    public function initialize(): void {
        parent::initialize();
        //  $this->Auth->allow(['logout','resetpass']);
    }

//    public function login() {
//        $loggeduser = $this->getRequest()->getSession()->read('Auth.User');
//        // echo debug($loggeduser);
//        //  echo debug ($this->Auth->redirectUrl());
//        if (isset($loggeduser)) {
//            return $this->redirect($this->Auth->redirectUrl());
//        } else {
//            $this->viewBuilder()->setLayout('login');
//            if ($this->request->is('post')) {
//                $user = $this->Auth->identify($this->request->getData());
//                //  debug ($user);
//                if (is_array($user)) {
//                    $this->Auth->setUser($user);
//                    $this->_loadsessions();
//                    return $this->redirect($this->Auth->redirectUrl());
//                } else {
//                    $this->Flash->error(__('Invalid username or password, try again'));
//                }
//            }
//        }
//    }


    public function login() {
        $this->viewBuilder()->setLayout('login');
        $result = $this->Authentication->getResult();
        //  debug($result);
        // If the user is logged in send them away.
        if ($result->isValid()) {
       
            //Load Session info for Views.
            $session = $this->getRequest()->getSession();
            $user = $this->Authentication->getIdentity();
            $session->write('Config.id', $user->ugroup_id);
            $session->write('Config.account_id', $user->account_id);
            $account = $this->getTableLocator()->get('Accounts')->get($user->account_id);
            $session->write('Config.company', $account->company_name);

            
            //Set last Login Time. 
            $time = date("Y-m-d h:i:s", time());
            $ntime = new FrozenTime($time, 'Asia/Riyadh');
            $query = $this->Users->query();
            $result = $query
                    ->update()
                    ->set([
                        $query->newExpr('login_count = login_count + 1'),
                        ['last_logged' => $time]
                            ]
                    )
                    ->where([
                        'id' => $this->getMyUID()
                    ])
                    ->execute();

            //end of sessions.
            
            
            $target = $this->Authentication->getLoginRedirect() ?? '/dashboards';
            return $this->redirect($target);
        }
        if ($this->request->is('post')) {
            $this->Flash->error('Invalid username or password');
        }
    }

    public function _loadsessions() {
//        debug($this->Auth->user('id'));
        $time = date("Y-m-d h:i:s", time());
        //   $this->loadModel('Users');
        $ntime = new FrozenTime($time, 'Asia/Riyadh');
        $query = $this->Users->query();
        $result = $query
                ->update()
                ->set([
                    $query->newExpr('login_count = login_count + 1'),
                    ['last_logged' => $time]
                        ]
                )
                ->where([
                    'id' => $this->getMyUID()
                ])
                ->execute();
//        $userinfo = $this->Users->get($this->getMyUID());
        //$this->Auth->write('User.account_id',$userinfo->account_id);
        //  debug($this->Auth->User('account_id'));
//        $session = $this->request->getSession();
        //    $session->write('Account.id', $userinfo->account_id);
//        $session->write('Ugroup.id', $userinfo->ugroup_id);

        $session = $this->getRequest()->getSession();
        $user = $this->Authentication->getIdentity();
        $session->write('Ugroup.id', $user->ugroup_id);
        $session->write('Auth.User.account_id', $user->account_id);

        $account = $this->getTableLocator()->get('Accounts')->get($user->account_id);
        //   debug($account);
        $session->write('Account.name', $account->company_name);
//        debug($session->read('Accunt.id'));
        return $result;
    }

//    function test() {
//        $this->viewBuilder()->setLayout('ajax');
//        $this->_loadsessions();
//        $session = $this->request->getSession();
//        debug($session->read('Accunt.id'));
//        debug($session->read('Accunt.id'));
//        debug($this->request->getSession()->read('Account.company_name')); 
//        debug($session->read('Accunt.company_name'));
//    }

    public function _update_group($user_id = null, $groups = array()) {
        $GroupUserTable = TableRegistry::get('GroupsUsers');
        $GroupUserTable->deleteAll(['user_id' => $user_id]);
        foreach ($groups as $key => $val) {
            //  debug ("Adding $user_id to $val");
            $usergroup = $GroupUserTable->newEmptyEntity();
            $usergroup = $GroupUserTable->patchEntity($usergroup, array('user_id' => $user_id, 'group_id' => $val));
            $GroupUserTable->save($usergroup);
        }
    }

//        public function logout-old() {
//        $session = $this->request->getSession();
//        $session->destroy();
//        return $this->redirect($this->Auth->logout());
//    }

    public function logout() {
        $this->Authentication->logout();
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    function groups() {
        $this->viewBuilder()->setLayout('ajax');
        $groups = TableRegistry::get('Groups');
        $query = $groups->find()
                ->select(['id', 'groupname']);
        $this->set('groups', $query->toList());
        $qdata = $this->request->getQuery();

        if (isset($qdata['DT_RowId'])) { //incase of EDIT.
            $userGroup = TableRegistry::get('GroupsUsers');
            $query = $userGroup->find()
                    ->where(['user_id' => $qdata['DT_RowId']])
                    ->toArray();
            $this->set('users', $query);
        } else { //Incase of NEW
            $this->set('users', array());
        }
    }

    public function staff() {
        $this->viewBuilder()->setLayout('ajax');
        // $this->set('form' ,null); //for Form
    }

    function setaccount() {
        $this->viewBuilder()->setLayout('setcompany');
        $this->set('form', null);
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $session = $this->request->getSession();
            $session->write('Accunt.id', $data['account_id']);
            $table = $this->getTableLocator()->get('Accounts');
            $select = $table->get($data['account_id']);
            $session->write('Account.name', $select->comapny_name);
            $this->redirect("/");
        }
    }
}
