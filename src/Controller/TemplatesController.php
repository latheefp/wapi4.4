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

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class TemplatesController extends AppController {

    var $uses = array('Templates');

    public function isAuthorized($user) {
        return true;
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        //       public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);
        $this->FormProtection->setConfig('unlockedActions', [ 'gettemplates']);
    }


    function refreshtemplates() {
        //  debug($this->Auth->user('id'))
        $data['account_id'] = $this->getMyAccountID();
        $FBsettings= $this->_getFBsettings($data);
        //      debug($data);
        $this->viewBuilder()->setLayout('ajax');
        $curl = curl_init();
        //   $WBAID=$this->_getsettings('WBAID');
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/' . $this->_getsettings('API_VERSION') . '/' . $FBsettings['WBAID'] . '/message_templates?&access_token=' . $FBsettings['ACCESSTOKENVALUE'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '{
        "messaging_product": "whatsapp",
        "status": "read",
      }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        //   debug($response);
        curl_close($curl);
        $templatelist = (json_decode($response, TRUE));
        $templates = $this->getTableLocator()->get('Templates');
//        $templates->updateAll((['status' => 'DELETED']), 'true');
//        $templates->updateAll((['active' => 0]), 'true');
        debug($templatelist);
        if (isset($templatelist['data'])) {
            foreach ($templatelist['data'] as $key => $val) {
                $info = TableRegistry::get('Templates')
                        ->find()
                        ->where([
                            'id' => $val['id'],
                        ])
                        ->first();
                if (empty($info)) {
                    $record = $templates->newEmptyEntity();
                    $record->name = $val['name'];
                    $record->account_id = $FBsettings['account_id'];
                    $record->language = $val['language'];
                    $record->status = $val['status'];
                    $record->category = $val['category'];
                    $record->id = $val['id'];
                    $record->active = 1;
                    //      $record->wbaid = $WBAID;
                    $record->template_details = $this->_gettemplate_details($val['name'], $FBsettings);
                    $templates->save($record);
                } else {
                    $templates->get($info->id);
                    $info->name = $val['name'];
                    $info->account_id = $FBsettings['account_id'];
                    $info->language = $val['language'];
                    $info->status = $val['status'];
                    $info->category = $val['category'];
                    $info->id = $val['id'];
                    $info->active = 1;
                    //      $info->wbaid = $WBAID;
                    $info->template_details = $this->_gettemplate_details($val['name'], $FBsettings);
                    $templates->save($info);
                }
                //           debug($info);
            }
        }
    }

    function _gettemplate_details($tname, $FBsettings) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            // CURLOPT_URL => 'https://graph.facebook.com/v14.0/115972771113915/message_templates?limit=3&access_token=EAAQjQnvrkg8BAFog8Ivl9neHUkrx4lZAuBA5o9pDpcwf9NLwksVvvoPUF84T9bLJbYNDlmD5GOpMlLvp7HhD6XwKyNp0cXr9xK3LxcFuM4tZBlNdwVCwZAZAowlSiBtEtw4fEmg71v0ikcQdLBpYPqZAMZCgoboVkoumz74g0BeopieCXCT4jWJREMZArj1evBRWSThWobghwZDZD&name=laguage_selection',
            CURLOPT_URL => 'https://graph.facebook.com/' . $FBsettings['API_VERSION'] . '/' . $FBsettings['WBAID'] . '/message_templates?limit=10&access_token=' . $FBsettings['ACCESSTOKENVALUE'] . '&name=' . $tname,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '{
  "messaging_product": "whatsapp",
  "status": "read",
  "message_id": "wamid.HBgMOTY2NTQ3MjM3MjcyFQIAERgSMzFBNTRFNzEwRjZGMTM1QTM5AA=="
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $FBsettings['ACCESSTOKENVALUE']
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        debug($response);
        return $response;
    }

    function index() {
        $this->set('PageLength', $this->_getsettings('pagination_count'));
        $this->set('feildsType', $this->_fieldtypes('templates'));
        $this->set('titleforlayout', "Templates");
        //  $this->refreshtemplates();
    }

    public function gettemplates() {
        $model = "Templates";
        $base_table = "templates";
        $this->viewBuilder()->setLayout('ajax');
        //   debug($this->request->getData());
        $query = $this->_set_template_query($this->request->getData(), $model, $base_table);
        //     debug($query);
        $data = $this->paginate = $query;
        $this->set('data', $this->paginate($model));
        $this->set('fieldsType', $this->_fieldtypes($base_table));
    }

    public function _set_template_query($querydata, $model, $base_table) {  //return array of quey based on passed values from index page search.
        $query = [
            'order' => [
                $model . '.id' => 'asc'
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
        $query['conditions']['AND'][] = array($model . ".account_id" => $this->getMyAccountID());
        $start = intval($querydata['start']);
        $query['page'] = ($start / $query['limit']) + 1;
        $query['order'] = array($querydata['columns'][$querydata['order']['0']['column']]['name'] . ' ' . $querydata['order']['0']['dir']);
        //     debug($query);
        return $query;
    }
}
