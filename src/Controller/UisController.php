<?php
namespace App\Controller;
//declare(strict_types=1);

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;



use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;

/**
 * Apis Controller
 *
 * @method \App\Model\Entity\Api[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UisController extends AppController {

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function isAuthorized($user) {
        return true;
    }

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);

        $formaction = $this->request->getParam('action');

        $this->FormProtection->setConfig('unlockedActions', array(
            $formaction
        ));

       // $this->Authentication->allowUnauthenticated(['conversation_analytics']);
    }





    public function getcontact() {
        $get = $this->request->getQuery();
        if ($get['query'] == "undefined") {
            $get['query'] = null;
        }

        $session = $this->request->getSession();
        $account_id = $this->getMyAccountID();
        $this->viewBuilder()->setLayout('ajax');
        $query = $this->getTableLocator()->get('RecentChats')->find();
        if (isset($get['query'])) {
            $query->where(
                    [
                        'AND' => ['account_id' => $account_id],
                        'OR' => [
                            'profile_name  LIKE' => '%' . $get['query'] . '%',
                            'contact_number  LIKE' => '%' . $get['query'] . '%',
                        ]
                    ]
            );
        }
//        $query->andwhere(['account_id' => $account_id]);

        $query->limit((int) $get['limit']);
        $query->page((int) $get['page']);
        //  $query->group('contact_number');
        $contact = $query->all()->toArray();
        // debug($contact);
        $this->set('contacts', $contact); //table row data
        //   debug($query);
    }

    function writelog($data, $type = null) {
        $file = LOGS . 'GrandWA' . '.log';
        #  $data =json_encode($event)."\n";  
        $time = date("Y-m-d H:i:s", time());
        $handle = fopen($file, 'a') or die('Cannot open file:  ' . $file); //implicitly creates file
        fwrite($handle, print_r("\n========================$type : $time============================= \n", true));
        fwrite($handle, print_r($data, true));
        fclose($handle);
    }

    function getmsg($contact_stream_id = null) {
        $session = $this->request->getSession();

        $this->set('contact_stream_id', $contact_stream_id);
        $this->viewBuilder()->setLayout('ajax');
        $query = $this->getTableLocator()->get('StreamViews')->find();
        $query->where(['contact_stream_id' => $contact_stream_id]);
        $query->andWhere(['account_id' => $this->getMyAccountID()]);
        $query->order(['modified' => 'DESC']);
        $query->limit(50);
        $messages = $query->all()->toArray();
        $this->set('messages', $query->all()->toArray()); //table row data
    }

    
    

    function _removeTrailingCommas($json) {
        // Remove trailing commas before closing brackets
        $json = preg_replace('/,\s*([\]}])/m', '$1', $json);
        return $json;
    }
    


    function index() {
        $apiTable = $this->getTableLocator()->get('ApiKeys');
        $apiKey = $apiTable->find()
                ->where(['account_id' => $this->getMyAccountID(), 'enabled' => true])
                ->first();
        $this->set('api_key', $apiKey);

        $this->viewBuilder()->setLayout('ajax');
    }

    function getrowhead($profile = null) {
        $this->viewBuilder()->setLayout('ajax');
        $this->set('profile', $profile);
    }


    function newindex() {

        $this->viewBuilder()->setLayout('ajax');
        $apiTable = $this->getTableLocator()->get('ApiKeys');
        $apiKey = $apiTable->find()
                ->where(['account_id' => $this->getMyAccountID(), 'enabled' => true])
                ->first();
        $this->set('api_key', $apiKey);

        $this->viewBuilder()->setLayout('ajax');
    }

}
