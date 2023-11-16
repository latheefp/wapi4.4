<?php

namespace App\Controller;

//App::uses('HttpSocket', 'Network/Http');
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
use Cake\Http\ServerRequest;
use HTTP\Request2;

class CampaignsController extends AppController {

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);
        $this->FormProtection->setConfig('unlockedActions', ['add','newcamp','getcampaign','attachments','getschedules','newsched','getstreams']);

    }

    var $uses = array('Campaigns');

    function viewimage($file_id = null) { //should be replaced by   viewrcvImage() functoin.
        if((!isset($file_id))&&(empty($file_id))) {
            $result['status']="failed";
            $result['msg']="Missing image ID";
            return $result;
        }




        $this->viewBuilder()->setLayout('ajax');
        $file = tmpfile();
        $file_path = stream_get_meta_data($file)['uri'];
        $curl = curl_init();
        $table = $this->getTableLocator()->get('CampaignForms');
        $query = $table->find()
                ->where(['fbimageid' => $file_id])
                ->first();
        //  debug ($query);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/' . $file_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: ' . $query->file_type,
                'Authorization: Bearer ' . $this->_getsettings('ACCESSTOKENVALUE')
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        $url = $result['url'];
        // debug($url);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_HEADER => 0,
            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
//            CURLOPT_FILE => $file_handle,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->_getsettings('ACCESSTOKENVALUE'),
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
            ),
        ));

        $raw = curl_exec($curl);

        curl_close($curl);
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $file_handle = fopen($file_path, 'x');

        fwrite($file_handle, $raw);

        fclose($file_handle);
        $response = $this->response->withFile($file_path,
                ['download' => true, 'name' => $query->field_value]
        );
        $response->withType($query->file_type);
        return $response;
    }

    public function isAuthorized($user) {
        return true;
    }

    function viewrcvImage() {
        //  $file_id = "6371848519559997";
        //   $filetype = "image/jpeg";
        $requestinfo = $this->request->getQuery();
        //debug($requestinfo);
        $file_id = $requestinfo['fileid'];
        $filetype = $requestinfo['type'];
        $stream_id = $requestinfo['id'];
//        $session = $this->request->getSession();
        $data['account_id'] =$this->getMyAccountID();
        $FBsettings = $this->_getFBsettings($data);

        $this->viewBuilder()->setLayout('ajax');
        $file = tmpfile();
        $file_path = stream_get_meta_data($file)['uri'];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/' . $file_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: ' . $filetype,
                'Authorization: Bearer ' . $FBsettings['ACCESSTOKENVALUE']
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        $url = $result['url'];
        // debug($url);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_HEADER => 0,
            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
//            CURLOPT_FILE => $file_handle,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBsettings['ACCESSTOKENVALUE'],
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
            ),
        ));

        $raw = curl_exec($curl);

        curl_close($curl);
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $file_handle = fopen($file_path, 'x');

        fwrite($file_handle, $raw);

        $streamRow = $this->getTableLocator()->get('Streams')->get($stream_id);
        $rcarray = json_decode($streamRow->recievearray, true);
        $message_array = $rcarray['entry'][0]['changes'][0]['value']['messages'][0];
       // debug($message_array);

        if ($message_array['type'] == "document") {
             $fname=$message_array['document']['filename'];
        } else {
            $ext = null;
            //debug($filetype);
            
            switch ($filetype) {
                case "video/mp4":
                    $ext = "mp4";
                    break;
                case "image/webp":
                    $ext = "webp";
                    break;
                case "image/jpeg":
                    $ext = "jpg";
                    break;
                case " audio/ogg":
                    $ext = "mp3";
                case " application/pdf":
                    $ext = "pdf";
                case " application/vnd.openxmlformats-officedocument.wordprocessingml.document":
                    $ext = "docx";
                case " text/csv":
                    $ext = "csv";
                case " text/plain":
                    $ext = "txt";
                default:
                    $ext = "unknown"; // Set a default extension or handle the case as needed.
                    break;
            }
            $fname=$message_array['from'].".".$ext;
        }








        fclose($file_handle);
        $response = $this->response->withFile($file_path,
                ['download' => true, 'name' => $fname]
        );
        $response->withType($filetype);
        return $response;
    }





    function index() {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('campaign_views'));
        $this->set('titleforlayout', "List Campaigns");
        $this->set('account_id',$this->getMyAccountID());
    }

    function newcamp() {
//  debug($_FILES);

        $this->viewBuilder()->setLayout('ajax');
        $table = $this->getTableLocator()->get('Campaigns');
        $result = [];
        if ($this->request->is('post')) {
            $data = $this->request->getData();
// debug($data);
            $row = $table->newEmptyEntity();
            $row->start_date = $data['start_date'];
            $row->end_date = $data['end_date'];
            $row->user_id = $this->getMyUID();
            $row->template_id = $data['template_id'];
            $row->campaign_name = $data['campaign_name'];
            if ($row->getErrors()) {
                $result['status'] = "failed";
                $result['msg'] = "Validation errors";
//$this->set('result', $result);
                $error = $row->getErrors();
            } else {
                if ($table->save($row)) {
                    $result['status'] = "success";
                    $result['msg'] = "New campaign " . $data['campaign_name'] . " been added";
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
//debug($error);
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

    function _saveform_delete($data, $id, $files) {
        $asset_path = ROOT . DS . 'upload/' . $id;
// debug($asset_path);
        mkdir($asset_path, 0700);
        $table = $this->getTableLocator()->get('CampainForms');

//  debug($files);
        foreach ($files as $key => $val) {
            if ($val == "undefined") {
                continue;
            }
            $row = $table->newEmptyEntity();
            $row->campaign_id = $id;
            $row->field_type = 'file';
            $row->field_name = $key; //File Field name
            $row->field_value = $val->getClientFilename(); //file Name
            $imagePath = $asset_path . "/" . ($this->_genrand(8));
            $val->moveTo($imagePath);
//                copy($path ,$copypath);
            $row->file_path = $imagePath;
            $row->file_type = $val->getClientMediaType();
            $row->file_size = $val->getSize();
            if ($table->save($row)) {
                print "File saved";
            } else {
                print "File Save failed";
                $error = $row->getErrors();
                dd($error);
            }
        }

//        debug($data);
    }

    public function getcampaign() {
        $model = "CampaignViews";
        $base_table = "campaign_views";
        $this->viewBuilder()->setLayout('ajax');
//   debug($this->request->getData());
        $query = $this->_set_camp_query($this->request->getData(), $model, $base_table);
        $data = $this->paginate = $query;
        $this->set('data', $this->paginate($model));
        $this->set('fieldsType', $this->_fieldtypes($base_table));
    }

    public function _set_camp_query($querydata, $model, $base_table) {  //return array of quey based on passed values from index page search.
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

    function deletecamp($id) {
        $this->viewBuilder()->setLayout('ajax');
        $table = $this->getTableLocator()->get('Campaigns');
        $camp = $table->findById($id)->firstOrFail();
        if ($table->delete($camp)) {
            $result['status'] = "success";
            $result['msg'] = "The record has been deleted";
        } else {
            $result['status'] = "failed";
            $result['msg'] = "Not able to delete the record";
        }
        $this->set('result', $result);
    }

    function attachments($id = null) {
        if ($this->request->is('post')) {
            $this->viewBuilder()->setLayout('ajax');
            $data = $this->request->getData();
            $files = $_FILES;
            $id = $data['id'];
            unset($data['id']);
            $table = $this->getTableLocator()->get('CampaignForms');
            //delete any existing entry in form before saving. 
            $conditions = ['Campaign_id' => $id];
            $deleteresult = $table->deleteAll($conditions);
            //         debug($deleteresult);
//            if ($result) {
//                echo 'Deletion successful.';
//            } else {
//                echo 'Deletion failed.';
//            }
            foreach ($files as $key => $val) {
                // 
                $table->deleteAll(['field_name' => $key, 'campaign_id' => $id]);
                if (($val == "undefined") || empty($val['tmp_name'])) {
                    unset($data[$key]);
                    continue;
                }
                //             debug($val);
                $keyarray = explode("-", $key);
                $row = $table->newEmptyEntity();
                $row->campaign_id = $id;
                $row->field_type = 'file';
                $row->field_name = $key; //File Field name
                $row->field_value = $val['name']; //file Name
                // $imagePath = $asset_path . "/" . ($this->_genrand(8));
                unset($data[$key]); //remove filename from post data array
                //   rename($val['tmp_name'], $imagePath);
                //  $row->file_path = $imagePath;
                $row->file_type = $val['type'];
                $row->file_size = $val['size'];
                $row->language = $keyarray[1];

                if ($table->save($row)) {
                    $this->_uploadtofb($row->id, $val['tmp_name']);
                } else {
                    print "File Save failed\n";
                    $error = $row->getErrors();
                    dd($error);
                }
            }
            if (!empty($data)) {
                foreach ($data as $key => $val) {
                    $keyarray = explode("-", $key);
                    $row = $table->newEmptyEntity();
                    $row->campaign_id = $id;
                    $row->field_type = 'variable';
                    $row->field_name = $key; //File Field name
                    $row->field_value = $val; //file Name
                    $row->language = $keyarray[2];
                    if ($table->save($row)) {
//                        print "Variable saved\n";
                    } else {
                        //     print "File Save failed \n";
                        //   $error = $row->getErrors();
                        //     dd($error);
                    }
                }
            }
            $this->redirect('/campaigns/');
        }
        $table = $this->getTableLocator()->get('Campaigns');
        $camp = $table->findById($id)->firstOrFail();
        $template_id = $camp->template_id;
        $table = $this->getTableLocator()->get('Templates');
        $this->set('camp', $camp);

        $query = $table->query()
                ->where(['id' => $template_id])
                ->first();

        $this->set('data', $query->toArray());
        $table = $this->getTableLocator()->get('CampaignForms');
        $query = $table->query()
                ->find('all')
                ->where(['campaign_id' => $id]);

        $this->set('formdata', $query->toArray());
    }

    function attachment($id = null) {
        if ($this->request->is('post')) {
            $this->viewBuilder()->setLayout('ajax');
            $data = $this->request->getData();
            $files = $_FILES;
            $id = $data['id'];
            unset($data['id']);
            $table = $this->getTableLocator()->get('CampaignForms');
            foreach ($files as $key => $val) {
                // 
                $table->deleteAll(['field_name' => $key, 'campaign_id' => $id]);
                if (($val == "undefined") || empty($val['tmp_name'])) {
                    unset($data[$key]);
                    continue;
                }
                //             debug($val);
                $keyarray = explode("-", $key);
                $row = $table->newEmptyEntity();
                $row->campaign_id = $id;
                $row->field_type = 'file';
                $row->field_name = $key; //File Field name
                $row->field_value = $val['name']; //file Name
                // $imagePath = $asset_path . "/" . ($this->_genrand(8));
                unset($data[$key]); //remove filename from post data array
                //   rename($val['tmp_name'], $imagePath);
                //  $row->file_path = $imagePath;
                $row->file_type = $val['type'];
                $row->file_size = $val['size'];
                $row->language = $keyarray[1];

                if ($table->save($row)) {
                    if($this->_uploadtofb($row->id, $val['tmp_name'])){
                        print "Upload success";
                    }else{
                        print "Upload failed";
                    }
                } else {
                    print "File Save failed\n";
                    $error = $row->getErrors();
                    dd($error);
                }
            }
            if (!empty($data)) {
                foreach ($data as $key => $val) {
                    $keyarray = explode("-", $key);
                    $row = $table->newEmptyEntity();
                    $row->campaign_id = $id;
                    $row->field_type = 'variable';
                    $row->field_name = $key; //File Field name
                    $row->field_value = $val; //file Name
                    $row->language = $keyarray[2];
                    if ($table->save($row)) {
                        print "Variable saved\n";
                    } else {
                        print "File Save failed \n";
                        $error = $row->getErrors();
                        dd($error);
                    }
                }
            }
            $this->redirect('/campaigns/');
        }
        $table = $this->getTableLocator()->get('Campaigns');
        $camp = $table->findById($id)->firstOrFail();
        $template_id = $camp->template_id;
        $table = $this->getTableLocator()->get('Templates');
        $this->set('camp', $camp);

        $query = $table->query()
                ->where(['id' => $template_id])
                ->first();

        $this->set('data', $query->toArray());
        $table = $this->getTableLocator()->get('CampaignForms');
        $query = $table->query()
                ->find('all')
                ->where(['campaign_id' => $id]);

        $this->set('formdata', $query->toArray());
    }

    function _uploadtofb($id, $path) {
       // debug($data);
        $session = $this->request->getSession();
        $data['account_id'] = $this->getMyAccountID();
        $FBsettings = $this->_getFBsettings($data);
        //     debug($id);
        $table = $this->getTableLocator()->get('CampaignForms');
        $query = $table->find()
                ->where(['id' => $id])
                ->first();
        //     debug($query);
        $curl = curl_init();
        //     debug($FBsettings);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/' . $FBsettings['API_VERSION'] . '/' . $FBsettings['phone_number_id'] . '/media',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('file' => new \CURLFILE("$path", $query->file_type, 'file'), 'messaging_product' => 'whatsapp'),
            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBsettings['ACCESSTOKENVALUE']
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $resparray = (json_decode($response, TRUE));
    //    debug($resparray);
        if (isset($resparray['id'])) {
            $fbimageid = $resparray['id'];
            $row = $table->get($id);
            $row->fbimageid = $fbimageid;
            if($table->save($row)){
                return true;

            }else{
                return false;
            }
            
        }else{
            return false;
        }
       
    }

    function schedules() {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('schedule_views'));
        $this->set('titleforlayout', "Schedules");
        $this->set('account_id',$this->getMyAccountID());
        
    }

    public function getschedules() {
        $model = "ScheduleViews";
        $base_table = "schedule_views";
        $this->viewBuilder()->setLayout('ajax');
        // debug($this->request->getData());
        $query = $this->_set_sched_query($this->request->getData(), $model, $base_table);
        $data = $this->paginate = $query;
        $this->set('data', $this->paginate($model));
        $this->set('fieldsType', $this->_fieldtypes($base_table));
    }

    public function _set_sched_query($querydata, $model, $base_table) {  //return array of quey based on passed values from index page search.
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
        $query['conditions']['AND'][] = array($model . ".account_id" => $this->getMyAccountID());

        // debug($query);
        return $query;
    }

    function newsched() {
        $this->viewBuilder()->setLayout('ajax');
        $table = $this->getTableLocator()->get('Schedules');
        $result = [];

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            //     debug ($data);
            $row = $table->newEmptyEntity();
            $row->user_id = $this->getMyUID();
            $row->name = $data['name'];
            $row->campaign_id = $data['campaign_id'];
            $row->account_id =$this->getMyAccountID();
            $row->status = "Loaded";
            if ($row->getErrors()) {
                $result['status'] = "failed";
                $result['msg'] = "Validation errors";
                $error = $row->getErrors();
            } else {
                if ($table->save($row)) {
                    $id = $row->id;
                    $result['status'] = "success";

                    //    $this->_updateschedcontacts($id, $data['contact_id']); //send to background.
                    //  $contact_json= json_encode($data['contact_id']);
                    $contact_csv = implode(",", $data['contact_id']);

//                    foreach($contact_ids as $id =>$contact){
//                        
//                    }
                    $cmd = ROOT . DS . "bin/cake Sendschedule -i $id -c $contact_csv > /dev/null 2>&1 &";
                    exec($cmd);
                    //             debug($cmd);
                    $result['msg'] = "New campaign " . $data['name'] . " been added with ID $cmd";
                } else {
                    $result['status'] = "failed";
                    $result['msg'] = "Not able to save the the Contact group";
                    $error = $row->getErrors();
                }
            }
        }
        $this->set('result', $result);
    }

    function sendmsg($sched_id) {
        $this->_sendmsg($sched_id);
    }

//    function _updateschedcontacts($id, $contact_id) {
//        $table = $this->getTableLocator()->get('ContactsSchedules');
//        $table->deleteAll(['schedule_id' => $id]);
//        $contact_contact_number_table = $this->getTableLocator()->get('ContactsContactNumbers');
//        foreach ($contact_id as $ckey => $cval) {
//            $query = $contact_contact_number_table->find()->innerJoinWith('ContactNumbers');
//            $query->where(['contact_id' => $cval])
//                    ->select([
//                        'ContactsContactNumbers.contact_id',
//                        'ContactsContactNumbers.contact_number_id',
//                        'ContactNumbers.mobile_number',
//                        'ContactNumbers.blocked'
//                    ])
//                    ->toArray();
//            foreach ($query as $key => $val) {
//                $blocked = $val->_matchingData['ContactNumbers']['blocked'];
//                if ($blocked == false) {
//                    $row = $table->newEmptyEntity();
//                    $row->schedule_id = $id;
//                    $row->contact_waid = $val->_matchingData['ContactNumbers']['mobile_number'];
//                    $table->save($row);
//                }
//            }
//        }
//    }

    function deletesched($id) {
        $this->viewBuilder()->setLayout('ajax');
        $contacts_schedules = $this->getTableLocator()->get('ContactsSchedules');
        $contacts_schedules->deleteAll(['schedule_id' => $id]);
        $table_schedule = $this->getTableLocator()->get('Schedules');
        if ($table_schedule->deleteAll(['id' => $id])) {
            $result['msg'] = "Schedule and history has been deleted";
            $result['status'] = "success";
        } else {
            $result['msg'] = "Not able to delete schedule";
            $result['status'] = "failed";
        }

        $this->set('result', $result);
    }

    function getscheddetails($id) {
        $this->viewBuilder()->setLayout('ajax');
        $table = $this->getTableLocator()->get('Streams');
        $total = $table->query()
                ->where(['schedule_id' => $id])
                ->count();
        //debug($total);
        $no_wa = $table->query()
                ->where(['schedule_id' => $id, 'has_wa' => 0])
                ->count();
        // debug ($no_wa);

        $this->set('total', $total);
        $this->set('no_wa', $no_wa);
        $this->set('id', $id);

        $schedtable = $this->getTableLocator()->get('Schedules');
        $schedule = $schedtable->get($id);
        // debug ($query);

        $table = $this->getTableLocator()->get('Campaigns');
        $camp = $table->findById($schedule->campaign_id)->firstOrFail();
        $template_id = $camp->template_id;
        $table = $this->getTableLocator()->get('Templates');
        $this->set('camp', $camp);

        $query = $table->query()
                ->where(['id' => $template_id])
                ->first();

        $this->set('data', $query->toArray());
        $table = $this->getTableLocator()->get('CampaignForms');
        $query = $table->query()
                ->find('all')
                ->where(['campaign_id' => $schedule->campaign_id]);

        $this->set('formdata', $query->toArray());
    }

    function schedulereport($id) {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('schedulestreamsviews'));
        $this->set('titleforlayout', "Schedule Report");
        $this->set('id', $id);
    }

    function getschedulereport() {
        $this->viewBuilder()->setLayout('ajax');
        $model = "Schedulestreamsviews";
        $base_table = "schedulestreamsviews";
        $this->viewBuilder()->setLayout('ajax');
        // debug($this->request->getData());
        $query = $this->_sched_report_query($this->request->getData(), $model, $base_table);
        $data = $this->paginate = $query;
        // debug($data); 
        $this->set('data', $this->paginate($model));
        $this->set('fieldsType', $this->_fieldtypes($base_table));
    }

    public function _sched_report_query($querydata, $model, $base_table) {  //return array of quey based on passed values from index page search.
        //  debug($querydata);
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

        $query['conditions']['AND'][] = array($model . ".schedule_id" => $querydata['schedule_id']);

        //  debug($query);
        return $query;
    }

    function test() {
        
    }

    function streams() {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('stream_views'));
        $this->set('titleforlayout', "StreamView");
    }

    function getstreams() {
        $model = "StreamViews";
        $base_table = "stream_views";
        $this->viewBuilder()->setLayout('ajax');
        $query = $this->_set_stream_query($this->request->getData(), $model, $base_table);
        // debug($query);
        $data = $this->paginate = $query;
        $this->set('data', $this->paginate($model));
        $this->set('fieldsType', $this->_fieldtypes($base_table));
    }

    public function _set_stream_query($querydata, $model, $base_table) {  //return array of quey based on passed values from index page search.
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
        foreach ($fields as $title => $props) {
            if (($props['viewable'] == true) && ($props['searchable'] == true)) {
                if (isset($querydata['search']['value'])) {
                    $query['conditions']['OR'][] = array($model . "." . $props['fld_name'] . ' LIKE' => '%' . $querydata['search']['value'] . '%');
                }
            }
        }
        $start = intval($querydata['start']);
        $query['page'] = ($start / $query['limit']) + 1;
        if (!empty($querydata['columns'][$querydata['order']['0']['column']]['name'])) {
            $query['order'] = array($querydata['columns'][$querydata['order']['0']['column']]['name'] . ' ' . $querydata['order']['0']['dir']);
        }
//        $session = $this->request->getSession();
        $query['conditions']['AND'][] = array($model . ".account_id" => $this->getMyAccountID());

        return $query;
    }

    function resend($id = null) {
        $this->viewBuilder()->setLayout('ajax');
        $stream = $this->getTableLocator()->get('Streams')->find()->where(['id' => $id])->first();
        $data['account_id'] = $stream->account_id;
        $FBSettings = $this->_getFBsettings($data);
//        debug($FBSettings);
        // debug($stream);

        $this->writelog($id, "Resending MSG");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/' . $FBSettings['API_VERSION'] . '/' . $FBSettings['phone_number_id'] . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $stream->sendarray,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBSettings['ACCESSTOKENVALUE'],
            ),
        ));

        $jsonresponse = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($jsonresponse, true);
        if (isset($response['error'])) {
            $result['status'] = "failed";
            $result['msg'] = $response['error']['message'];
        } else {
            $result['status'] = "success";
            //       $result['msg']=$response['error']['message'];
        }
        //  debug($response);
        //       debug($response);
        $this->writelog($response, "Despatch response of Resend msg");
//
        $table = $this->getTableLocator()->get('Streams');
        $row = $table->get($id);
        if (isset($response['messages'][0]['id'])) {
            $row->messageid = $response['messages'][0]['id'];
            $row->type = "send";
            $row->has_wa = true;
            $row->success = true;
            $row->result = $jsonresponse;
            $table->save($row);
        } else {
            $this->writelog($response, "Response error");
            $row->type = "send";
            $row->has_wa = false;
            $row->result = $jsonresponse;
            $row->success = false;
            //  $row->sendarray = json_encode($sendarray);
            $table->save($row);
        }

        $this->set('result', $result);
    }

    function getstreamdetails($id = null) {
        $session = $this->request->getSession();
        $ugroup_id = intval($session->read('Auth.ugroup_id'));
        $this->set('ugroup_id',$ugroup_id);
        $this->viewBuilder()->setLayout('ajax');
        $table = $this->getTableLocator()->get('Streams');
        $query = $table->query()
                ->where(['id' => $id])
                ->first();
        $this->set('id', $id);
        $this->set('data', $query);
//        $session = $this->request->getSession();
        $data['account_id'] = $this->getMyAccountID();
        $FBsettings = $this->_getFBsettings($data);
        $this->set('FBsettings', $FBsettings);

        $updateTable = $this->getTableLocator()->get('StreamsUpdates');
        $updatequery = $updateTable->find()
                ->where(['stream_id' => $id])
                ->contain('Users')
                ->all();
        $this->set('updates', $updatequery);
        
    }

    function updatecomment() {
        $this->autoRender = false;
        $data = $this->request->getData();
        $data['user_id'] = $this->getMyUID();

        $updateTable = $this->getTableLocator()->get('StreamsUpdates');
        $updateStream = $updateTable->newEmptyEntity();
        $updateStream = $updateTable->patchEntity($updateStream, $data);
        if ($updateTable->save($updateStream)) {

            //ALTER TABLE `streams` ADD `commented` BOOLEAN NOT NULL DEFAULT FALSE AFTER `errors`; 
            $streamtable = $this->getTableLocator()->get('Streams');
            $row = $streamtable->get($data['stream_id']);
            $row->commented = true;
            $streamtable->save($row);

            $updatequery = $updateTable->find()
                    ->where(['StreamsUpdates.id' => $updateStream->id])
                    ->contain('Users')
                    ->all();
            $this->set('updates', $updatequery);
            $message = ['message' => 'Update saved successfully'];
            $message['data'] = $updatequery->toArray();
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode($message));
        } else {
            $this->response = $this->response->withType('application/json')->withStatus(400);
            $this->response = $this->response->withStringBody(json_encode(['message' => 'Failed to save update']));
        }
        return $this->response;
    }

    function blockme($stream_id) {
        $streamtable = $this->getTableLocator()->get('Streams');
        $row = $streamtable->get($stream_id);
        debug($row);
    }

}
