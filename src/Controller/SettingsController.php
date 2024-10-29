<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use Cake\Event\EventInterface;

//use Cake\Http\ResponseFactory;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 */
class SettingsController extends AppController
{

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->FormProtection->setConfig('unlockedActions', [
            'getapis',
            'costupload'
        ]);

        //   $this->Authentication->allowUnauthenticated(['getuserdata']);

        $formaction = $this->request->getParam('action');

        $this->FormProtection->setConfig('unlockedActions', array(
            $formaction
        ));
    }



    function listgroups()
    {
        //TODO:implement permissions.

        $this->loadModel('Ugroups');
        $groups = TableRegistry::get('Ugroups');
        $query = $groups->find('all');
        $results = $query->all();
        $this->set('groups', $results);
        $this->set('pagination_count', $this->_getsettings('pagination_count'));
    }

    public function newuser()
    {
        $this->set('user', null);
        $this->set('action', '/settings/ajxnewuser');
        $this->set('titleforlayout', "Add New User");
    }

    function ajxnewuser()
    {
        $this->viewBuilder()->setLayout('ajax');
        $userTable = $this->getTableLocator()->get('Users');

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $data['account_id'] = $this->getMyAccountID();
            $admin_id = $this->getMyGID();
            //      debug($data);
            //if AdminID=1, Super user can be added. else no.
            if (($admin_id != 1) && ($data['ugroup_id'] == 1)) {
                $result['status'] = "failed";
                $result['msg'] = "Unauthorized Group request, Super Admin";
                $this->set('result', $result);
                return $result;
            }
            $user = $userTable->newEntity($data);
            if ($user->getErrors()) {
                $result['status'] = "failed";
                $result['msg'] = "Validation errors";
                $this->set('result', $result);
                return $result;
            }
            //   debug($user);
            if ($userTable->save($user)) {
                $result['status'] = "success";
                $result['msg'] = "User has been added";
            } else {
                $result['status'] = "failed";
                $result['msg'] = "Not able to save the user";
                dd($user->getErrors());
            }
            $this->set('result', $result);
        }
        $this->set('user', $user);
    }

    public function groupadd()
    {
        if (!$this->_checkallowed('add_group')) {
            $this->redirect("/550");
        }
        //TODO implement the permission check.
        $this->viewBuilder()->setLayout('ajax');
        $data = $this->request->getData()['datafield'];
        $groupsTable = TableRegistry::get('Groups');
        $groups = $groupsTable->newEntity();
        $return = array();
        $groups->groupname = $data;
        if ($groupsTable->save($groups)) {
            $return['status'] = "Success";
            $return['message'] = "$data has been added to group.";
        } else {
            $return['status'] = "error";
            $return['message'] = "Not able to save the record. try again.";
        }
        $this->set('result', $return);
    }

    public function listusers()
    {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('Users'));
        $this->set('titleforlayout', "User Management");
    }

    public function getuserdata()
    {
        $this->viewBuilder()->setLayout('ajax');
        $query = $this->_set_user_query($this->request->getData());
        $data = $this->paginate = $query;
        $this->set('users', $this->paginate('Users'));
        $this->set('fieldsType', $this->_fieldtypes('Users'));
    }

    public function _set_user_query($querydata)
    {  //return array of quey based on passed values from index page search.
        // debug($querydata);
        $query = [
            'order' => [
                'User.id' => 'asc'
            ]
        ];
        if (isset($querydata['length'])) {
            $query['limit'] = intval($querydata['length']);
        } else {
            $query['limit'] = $this->_getsettings('pagination_count');
        }
        $query['page'] = (intval($querydata['start'] / $query['limit'])) + 1;
        $query['order'] = array($querydata['columns'][$querydata['order']['0']['column']]['name'] . ' ' . $querydata['order']['0']['dir']);
        if (isset($querydata['search']['value'])) {
            $query['conditions']['OR'][] = array('Users.username LIKE' => '%' . $querydata['search']['value'] . '%');
            $query['conditions']['OR'][] = array('Users.email LIKE' => '%' . $querydata['search']['value'] . '%');
        }

        //   $session = $this->request->getSession();
        $query['conditions']['AND'][] = array('Users.account_id' => $this->getMyAccountID());

        // debug($query);
        return $query;
    }

    public function login()
    {
        $loggeduser = $this->request->session()->read('Auth.User');
        if (isset($loggeduser)) {
            return $this->redirect($this->Auth->redirectUrl());
        }
        $this->viewBuilder()->layout('login');
        if ($this->request->is('post')) {
            $user = $this->Auth->identify($this->request->getData());
            if ($user) {

                $this->Auth->setUser($user);
                $this->_logincount();
                //   return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('Invalid username or password, try again'));
        }
    }

    public function _logincount()
    {
        //   echo debug ("You are here");
        $loggeduser = $this->request->session()->read('Auth.User');
        //   echo debug($loggeduser);
        $time = date("Y-m-d h:i:s", time());
        // echo debug($time);
        $ntime = new Time($time, 'Asia/Riyadh');

        $query = $this->Users->query();
        $result = $query
            ->update()
            ->set(
                [
                    $query->newExpr('login_count = login_count + 1'),
                    ['last_logged' => $time]
                ]
            )
            ->where([
                'id' => $this->Auth->user('id')
            ])
            ->execute();
        return $result;
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    public function isAuthorized($user)
    {
        //auth check
        //return boolean
        return true;
    }

    public function initialize(): void
    {
        parent::initialize();
        //        $this->Auth->allow(array('validatemember'));
    }

    public function permissions($group_id = null)
    {
        if (!isset($group_id)) {
            $group_id = 1;
        }
        //  debug ($group_id);
        if (!$this->_checkallowed('perm_mgt')) {
            $this->redirect("/550");
        }
        $PermTable = TableRegistry::get('Permissions');
        $pertypearray = $PermTable
            ->find()
            ->distinct(['category'])
            ->toList();
        $this->set('action_types', $pertypearray);

        $permissionarray = $PermTable
            ->find()
            ->toList();
        $this->set('permissionarray', $permissionarray);

        $GroupPermissTable = TableRegistry::get('UgroupsPermissions');
        $session = $this->request->getSession();

        $query = $GroupPermissTable->find('all', array(
            'conditions' => [
                'ugroup_id' => $group_id,
                'AND' => array(
                    'account_id' => $session->read('Accunt.id')
                )
            ],
        ));

        $group_permission = $query->toArray();
        //  pr($group_permission);
        $permission = array();
        foreach ($group_permission as $key => $val) {
            $permission[$val->permission_id] = $val->permission_id;
        }
        $this->set('group_permission', $permission);

        $groups = TableRegistry::get('Ugroups');
        $query = $groups->find()
            ->select(['id', 'groupname']);
        $this->set('groups', $query->toArray());
        $this->set('group_id', $group_id);
    }

    public function submitpermission()
    {
        $uid = $this->getRequest()->getSession()->read('Auth.User.id');
        if ((!$this->_checkallowed('perm_mgt')) || ($uid != 1)) {
            $result = array();
            $result['status'] = 'failed';
            $result['message'] = "You are not permitted change permissions";
        }
        $this->viewBuilder()->setLayout('ajax');
        $result = array();
        $result['status'] = 'success';
        $result['message'] = "Data Saved.";
        $data = $this->request->getData();
        if (isset($data['group_id'])) {
            $group_id = $data['group_id'];
            unset($data['group_id']);
            $GroupsPermissionsTable = TableRegistry::get('GroupsPermissions');
            $GroupsPermissionsTable->deleteAll(['group_id' => $group_id]);
            foreach ($data as $key => $val) {
                $grouppermission = $GroupsPermissionsTable->newEmptyEntity();
                $grouppermission->group_id = $group_id;
                $grouppermission->permission_id = $key;
                if ($GroupsPermissionsTable->save($grouppermission)) {
                    $result['status'] = 'success';
                    $result['message'] = "Permission has been update";
                } else {
                    $result['status'] = 'error';
                    $result['message'] = "Failed to update the permission.";
                }
            }
        }
        $this->set('result', $result);
    }

    function viewmgt()
    {
        $this->set('titleforlayout', "View Management");
    }

    function validatemember()
    {
        $this->viewBuilder()->setLayout('ajax');
        $member = ($this->request->getData('login_id'));
        // $member="latheefp";
        if (empty($member)) {
            print "fail";
            return null;
        }
        $memberTable = TableRegistry::get('Members');

        $existing = $memberTable
            ->find()
            ->where(['login_id' => $member])
            ->toList();
        if (count($existing) == 0) {
            print "success";
        } else {
            print "fail";
        }
        //        $exists = $this->Member->findByLoginId($member)->firstOrFail();
//        $this->set(compact($exists));
    }

    public function fieldtypes($table_name = null)
    {
        // debug($this->_fieldtypes($table_name));
    }

    function view($view)
    {
        if (!$this->_checkpermission($action = "manage_account")) {
            $this->redirect('/page403');
        }
        $this->set('permission', $this->_get_permission($view, null, null, true));

        $PageLength = $this->Session->read('User.row_per_page');
        if ($PageLength == 0) {
            $PageLength = $this->_getsettings('def_page_limit');
        }
        $this->set('PageLength', $PageLength);
        $this->set('query', $this->request->query);
        $this->set('titleforlayout', $view);
        $this->set('view', $view);
    }

    function validate($action = null, $model = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $data = $this->request->getData();
        $result = array();
        foreach ($data as $field => $value) {
            $validation = $this->validateField($model, $field, $value, $action);
            $result[] = $validation;
            //debug($validation);
        }
        $this->set('result', $result);
    }

    function fieldsettings()
    {
        //  $this->loadModel('Field');
        $query = $this->getTableLocator()->get('Flagships')->find();
        $query->select(['tbl_name'])
            ->distinct(['tbl_name']);
        $temp = [];
        foreach ($query as $key => $val) {
            //  debug ($val->tbl_name);
            $temp[$val->tbl_name] = $val->tbl_name;
        }
        //   debug($query->toList());
//                $tables=$this->Field->find('all',
//                        array(
//                            'fields'=>array('table'), 
//        //                    'group' => '`table`'
//                            )
//                        );
//                foreach ($tables as $key =>$val){
//                 //   echo debug ($val);
//                    $temp[$val['Field']['table']]=$val['Field']['table'];
//                }
        $this->set('tables', $temp);
        $query = $this->getTableLocator()->get('Groups')->find();
        $query->select(['id', 'groupname']);
        //                ->where(['company_id' => $this->request->getSession()->read('Company.id')]);
        $this->set('groups', $query->toList());
    }

    function apis()
    {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('apiviews'));
        $this->set('titleforlayout', "Apiviews");
    }

    public function getapis()
    {
        $model = "Apiviews";
        $base_table = "apiviews";
        $this->viewBuilder()->setLayout('ajax');
        // debug($this->request->getData());
        $query = $this->_set_api_query($this->request->getData(), $model, $base_table);
        $data = $this->paginate = $query;
        $this->set('data', $this->paginate($model));
        $this->set('fieldsType', $this->_fieldtypes($base_table));
    }

    public function _set_api_query($querydata, $model, $base_table)
    {  //return array of quey based on passed values from index page search.
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
        $start = intval($querydata['start']);
        //  debug($querydata['columns'][$querydata['order']['0']['column']]['name']);
        $query['page'] = ($start / $query['limit']) + 1;
        if (!empty($querydata['columns'][$querydata['order']['0']['column']]['name'])) {
            $query['order'] = array($querydata['columns'][$querydata['order']['0']['column']]['name'] . ' ' . $querydata['order']['0']['dir']);
        }

        $session = $this->request->getSession();
        $group_id = intval($session->read('Auth.ugroup_id'));
        if ($group_id == 1) {

        } else {
            $query['conditions']['AND'][] = array($model . ".account_id" => $session->read('Auth.User.account_id'));
        }


        //  debug($query);
        return $query;
    }

    function newapi()
    {
        $this->viewBuilder()->setLayout('ajax');
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $table = $this->getTableLocator()->get('ApiKeys');
            $row = $table->newEmptyEntity();
            $row->user_id = $this->Auth->user('id');
            $row->api_name = $data['api_name'];
            $row->enabled = $data['enabled'];
            $row->ip_list = json_encode($data['ip_list']);
            $row->api_key = $this->_genrand(64);
            // debug($data);
            if ($row->getErrors()) {
                $result['status'] = "failed";
                $result['msg'] = "Validation errors";
                $error = $row->getErrors();
            } else {
                if ($table->save($row)) {
                    $result['status'] = "success";
                    $result['msg'] = "New campaign " . $data['api_name'] . " been added";
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

        $this->set('result', $result);
    }

    function deleteapi($id)
    {
        $this->viewBuilder()->setLayout('ajax');
        $table = $this->getTableLocator()->get('ApiKeys');
        $row = $table->findById($id)->firstOrFail();
        if ($table->delete($row)) {
            $result['status'] = "success";
            $result['msg'] = "The record has been deleted";
        } else {
            $result['status'] = "failed";
            $result['msg'] = "Not able to delete the record";
        }
        $this->set('result', $result);
    }

    function getapkinfo($id)
    {
        $this->viewBuilder()->setLayout('ajax');
        $table = $this->getTableLocator()->get('ApiKeys');
        $query = $table->get($id);
        //  debug ($query);       
        $this->set('status', $query->enabled);
    }

    function toggleapistate($id)
    {
        $this->viewBuilder()->setLayout('ajax');
        $table = $this->getTableLocator()->get('ApiKeys');
        $query = $table->get($id);
        if ($query->enabled == true) {
            $query->enabled = false;
        } else {
            $query->enabled = true;
        }
        $table->save($query);
        $this->set('status', $query->enabled);
    }

    public function edituser($id = null)
    {

        #only super admin is allowed to edit all users, others are allowed to edit only their account. 
        $session = $this->request->getSession();
        $admin_id = $session->read('Auth.User.ugroup_id');

        $this->set('user', null);
        $this->set('action', '/settings/ajxedituser');
        $this->set('titleforlayout', "Edit User");
        if ($admin_id == 1) {  //Super User
            $user = $this->getTableLocator()->get('Users')->get($id);
        } else {
            $account_id = $this->getMyAccountID();
            $user = $this->getTableLocator()
                ->get('Users')
                ->find()
                ->where(['account_id' => $account_id, 'id' => $id])
                ->first();
        }
        if (empty($user)) {
            $this->redirect("/404");
        }
        $this->set('user', $user);
    }

    function ajxedituser()
    {
        $data = $this->request->getdata();
        //debug($data);
        $this->viewBuilder()->setLayout('ajax');
        $this->loadModel('Users');
        $data = $this->Users->get($data['id']);

        $session = $this->request->getSession();
        $data['account_id'] = $session->read('Auth.User.account_id');
        $admin_id = $session->read('Auth.User.ugroup_id');
        //if AdminID=1, Super user can be added. else no.
        if (($admin_id != 1) && ($data['ugroup_id'] == 1)) {
            $result['status'] = "failed";
            $result['msg'] = "Unauthorized Group request, Super Admin";
            $response = $this->response
                ->withStringBody(json_encode($result))
                ->withStatus(200)
                ->withType('application/json');

            return $response;
        }

        $data = $this->Users->patchEntity($data, $this->request->getData());

        if ($this->Users->save($data)) {
            $result['status'] = "success";
            $result['msg'] = "Update has been saved";
        } else {
            $result['status'] = "failed";
            $result['msg'] = "Validation errors";
        }
        $this->set('result', $result);
    }

    function deletuser($id)
    {
        $this->viewBuilder()->setLayout('ajax');
        if ($id == 1) {
            $result['status'] = "failed";
            $result['msg'] = "You cannot delete super User";
        } else {
            if ($this->_checkallowed('admin_users')) {
                //  $this->loadModel('Users');
                $userTable = $this->getTableLocator()->get('Users');
                $session = $this->request->getSession();
                $account_id = $this->getMyAccountID();
                //  debug($account_id);
                $user = $userTable->find()
                    ->where(['id' => $id])
                    ->andWhere(['account_id' => $account_id])
                    ->first();
                //    debug($user);
                if (isset($user)) {
                    //     debug($user);
                    if ($userTable->delete($user)) {
                        $result['status'] = "success";
                        $result['msg'] = "User has been deleted";
                    } else {
                        $result['status'] = "failed";
                        $result['msg'] = "Not able to delete the user";
                    }
                } else {
                    $result['status'] = "failed";
                    $result['msg'] = "Not able to delete the user, not authorized";
                }
            } else {
                $result['status'] = "failed";
                $result['msg'] = "Not permitted";
            }
        }

        $this->set('result', $result);
    }

    function switchCompany($account_id)
    {
        $this->viewBuilder()->setLayout('ajax');
        $session = $this->request->getSession();
        $u_group_id = $this->request->getSession()->read('Auth.ugroup_id');
        if ($u_group_id == 1) {
            $session->write('Config.account_id', $account_id);
            $result['msg'] = "Account has been switched";
            $result['status'] = "success";
            $table = $this->getTableLocator()->get('Accounts');
            $select = $table->get($account_id);
            $session->write('Config.company', $select->company_name);
        } else {
            $result['msg'] = "You are not allowed to switch";
            $result['status'] = "failed";
        }

        $this->set('result', $result);
    }

    function newaccount()
    {
        $session = $this->request->getSession();
        $u_group_id = $session->read('Auth.User.ugroup_id');
        if ($u_group_id == 1) {


        } else {
            $this->redirect('/page403');
        }
    }




    function pricing()
    {
        $MyUID = $this->getMyGID();
        if ($MyUID !== "1") {
            $response = ['status' => 'failed', 'message' => 'You are allowed to access this page.'];
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($response));
        }
        // Capture the search query from the GET request
        $query = $this->request->getQuery('q'); // 'q' is the name of the search input field
        $status = $this->request->getQuery('status');
        $this->loadModel('PriceCards');
        // Base query with the 'Departments' relationship
        $conditions = [];

        //     $conditions['AND'][] = [
        //   //      'PriceCards.account_id' => $this->getMyAccountID()      
        //     ];

        // Check if a search query exists
        if (!empty($query)) {

            $conditions['OR'] = [
                'PriceCards.country LIKE' => '%' . $query . '%',
                'PriceCards.country_code LIKE' => '%' . $query . '%'
            ];
        }
        // if (!empty($status)) {

        //     if($status=="unblocked"){
        //         $conditions['AND'][] = [
        //             'PriceCards.camp_blocked' => 0         
        //         ];
        //     }elseif($status == "blocked"){
        //         $conditions['AND'][] = [
        //             'PriceCards.camp_blocked' => 1         
        //         ];
        //     }

        // }

        // Set up pagination and apply search conditions if any
        $this->paginate = [
            //     'contain' => ['Users'],
            'conditions' => $conditions, // Apply the conditions
        ];

        // Paginate the results
        $prices = $this->paginate($this->PriceCards);

        // Pass the results and search query to the view
        $this->set(compact('prices', 'query'));
    }

    // src/Controller/SettingsController.php

    public function costupload()
    {
        $this->request->allowMethod(['post']); // Only allow POST requests

        $MyUID = $this->getMyGID();
        if ($MyUID !== "1") {
            $response = ['status' => 'failed', 'message' => 'You are not allowed to access this page.'];
            return $this->response
                ->withType('application/json')
                ->withStatus(403) // Forbidden
                ->withStringBody(json_encode($response));
        }
        $nomatch = [];
        $failed = [];
        $uploadtype = $this->request->getData('file_type');
        $file = $this->request->getData('upload_file');

        // Check if file is a CSV
        $fileType = $file->getClientMediaType();
        if ($fileType !== 'text/csv') {
            $response = ['success' => false, 'message' => 'Only CSV files are allowed.'];
            return $this->response
                ->withType('application/json')
                ->withStatus(400) // Bad request
                ->withStringBody(json_encode($response));
        }


        // Save file to a temporary location
        $uploadPath = WWW_ROOT . 'files' . DS . 'uploads' . DS;
        $fileName = $file->getClientFilename();
        $file->moveTo($uploadPath . $fileName);
        $PriceCardTable = $this->getTableLocator()->get('PriceCards');
        //debug($fileType);
        if (!empty($this->request->getData('upload_file'))) {
            switch ($uploadtype) {
                case "price_chart":
                    // Process CSV (reading rows, etc.)

                    $PriceCardTable->updateAll(
                        ['updated' => false], // Set others_category to true
                        [] // Empty array means all rows
                    );

                    if (($handle = fopen($uploadPath . $fileName, "r")) !== false) {
                        // Read each row and process it
                        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                            if (count($data) != 3) {
                                $response = ['status' => 'failed', 'message' => 'The fileds counts are not matching 3, but ' . count($data)];
                                return $this->response
                                    ->withType('application/json')
                                    ->withStatus(403) // Forbidden
                                    ->withStringBody(json_encode($response));
                            }
                            // The CSV order is:
                            // Market, Currency, Marketing, Utility, Authentication, Authentication-International, Service


                            // debug($data);
                            $market = $data[0];

                            // Extract the numeric columns, ignoring "n/a"
                            $marketing = $data[2] === 'n/a' ? null : (float) $data[2];
                            $utility = $data[3] === 'n/a' ? null : (float) $data[3];
                            $authentication = $data[4] === 'n/a' ? null : (float) $data[4];
                            $authentication_international = $data[5] === 'n/a' ? null : (float) $data[5];
                            $service = $data[6] === 'n/a' ? null : (float) $data[6];

                            // Find the largest value among the numeric columns
                            $values = array_filter([$marketing, $utility, $authentication, $authentication_international, $service], fn($v) => $v !== null); // filter out null values
                            $maxValue = !empty($values) ? max($values) : null;

                            // Replace 'n/a' values with the max value
                            $marketing = $marketing ?? $maxValue;
                            $utility = $utility ?? $maxValue;
                            $authentication = $authentication ?? $maxValue;
                            $authentication_international = $authentication_international ?? $maxValue;
                            $service = $service ?? $maxValue;

                            // Step 1: Check if a record exists for this country
                            $matching = $PriceCardTable->find()
                                ->where(['market' => $market])
                                ->first(); // Fetch the first result or null if not found


                            //   debug($matching->sql());    
                            //echo $matching->sql();

                            if ($matching) { // Edit existing record
                                $matching->marketing = $marketing; // Correct the spelling of 'marketing'
                                $matching->utility = $utility;
                                $matching->authentication = $authentication;
                                $matching->authentication_international = $authentication_international; // Use underscores for the column name
                                $matching->service = $service;
                                $matching->updated = true; // Mark it as not others_category

                                if (
                                    !$PriceCardTable->updateAll(
                                        [
                                            'marketing' => $marketing, // Correct the spelling of 'marketing'
                                            'utility' => $utility,
                                            'authentication' => $authentication,
                                            'authentication_international' => $authentication_international,
                                            'service' => $service,
                                            'updated' => true // Mark it as not others_category
                                        ], // Set others_category to true
                                        ['market' => $market] // Empty array means all rows
                                    )
                                ) {

                                    $failed[] = $market;
                                }

                            } else { // Add new record if it doesn't exist
                                //   debug("No match for $market");
                                //     debug($matching);
                                $nomatch[] = $market;
                            }
                        }

                        fclose($handle);
                    }

                    // Respond with success
                    $response = ['success' => true, 'message' => 'File has been uploaded and processed.', 'nomatch' => $nomatch, 'failed' => $failed];
                    return $this->response
                        ->withType('application/json')
                        ->withStatus(200) // Success
                        ->withStringBody(json_encode($response));
                    break;

                case "country_mapping":
                    if (($handle = fopen($uploadPath . $fileName, "r")) !== false) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                            if (count($data) != 3) {
                                $response = ['status' => 'failed', 'message' => 'The fileds counts are not matching 3, but ' . count($data)];
                                return $this->response
                                    ->withType('application/json')
                                    ->withStatus(403) // Forbidden
                                    ->withStringBody(json_encode($response));
                            }
                            if($data[0]=="All other countries"){
                                $data[2]=00; #country code for others.
                            }
                            if(!isset($data[1])){
                                $data[1]=$data[0]; //if no market set, country is the market. 
                            }

                        

                            
                            debug($data);
                            $matching = $PriceCardTable->find()
                                ->where(['country' => $data[0]])
                                ->first(); // Fetch the first result or null if not found

                            if ($matching) {
                                if ($matching->market != $data[1]) {
                                    debug("No match $matching->market and " . $data[1]);
                                    $matching->market = $data[1];
                                    $matching->country_code = $data[2];
                                    if(!$PriceCardTable->save($matching)){
                                        debug($matching->getErrors());
                                    }
                                }

                            } else {
                                $newrecord = $PriceCardTable->newEmptyEntity();
                               
                                $newrecord->market = $data[1];
                                $newrecord->country_code = $data[2];
                                $newrecord->country = $data[0];
                                if (!$PriceCardTable->save($newrecord)) {
                                    debug($newrecord->getErrors());
                                }
                            }
                        }
                    }


                    $response = ['success' => true, 'message' => 'Country mapping has been uploaded and processed.', 'nomatch' => $nomatch, 'failed' => $failed];
                    return $this->response
                        ->withType('application/json')
                        ->withStatus(200) // Success
                        ->withStringBody(json_encode($response));

                    break;

                default:
                    $response = ['success' => failed, 'message' => 'Wrong file type selected ' . $fileType];
                    return $this->response
                        ->withType('application/json')
                        ->withStatus(200) // Success
                        ->withStringBody(json_encode($response));

            }
        } else {
            $response = ['success' => false, 'message' => 'No file uploaded.'];
            return $this->response
                ->withType('application/json')
                ->withStatus(400) // Bad request (missing file)
                ->withStringBody(json_encode($response));
        }
    }
}
