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

//    public function index() {
//        $this->viewBuilder()->setLayout('ajax');
//    }
    //Configure::read(‘var_name’);



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
        // $this->set('messages', $query->all()->toArray()); //table row data
        //   debug($messages);
        // foreach ($messages as $key => $val) {
        //     if (isset($val->sendarray)) {
        //         $val->msg = $this->arraytomsg($val->sendarray);
        //     } else { //if the message is recived.
        //         $val->msg = $val->recievearray;
        //     }
        // }
        $this->set('messages', $query->all()->toArray()); //table row data
    }

    // function arraytomsg($sendarray) {
    //   //  debug($sendarray);
    //     $encoding = mb_detect_encoding($sendarray);
    //     if ($encoding !== 'UTF-8') {
    //         $json = utf8_encode($sendarray);
    //     }

    //     $send_array = json_decode($this->_removeTrailingCommas($sendarray), true);
    //     // debug($send_array);
    //      $msg=null;
    //     switch ($send_array['type']) {
    //         case "template":
    //             $template_name = $send_array['template']['name'];
    //   //            debug($template_name);
    //             $template_info = $this->getTableLocator()->get('Templates')->find()->where(['name' => $template_name])->toArray();

    //             // debug($template_info);
    //             if (!empty($template_info)) {
    //                 $template_details = json_decode($template_info[0]['template_details'], true);
    //                 //  debug($template_details);
    //                 $tbutton = null;
    //                 $tbody = null;
    //                 $theader = null;
    //                 if (isset($template_details['data'])) {
    //                  //     debug($template_details['data'][0]['components']);
    //                     foreach ($template_details['data'][0]['components'] as $key => $val) {
    //                         switch ($val['type']) {
    //                             case "HEADER":
    //                              //   debug($val);
    //                                 if(($val['format']=="text")){
    //                                     $theader = "<b>" . $val['text'] . "</b>";
    //                                 ///    debug($val);
    //                                 }elseif($val['format']=="IMAGE"){
    //                                   //  debug($val);
    //                                   $theader='<img src="'.$val['example']['header_handle'][0].'">';
    //                                 }
                                    
    //                                 break;
    //                                 ;
    //                             case "BODY":
    //                                 $tbody = $val['text'];
    //                                 break;
    //                                 ;
    //                             case "BUTTONS":
    //                                 foreach ($val['buttons'] as $bkey => $bval) {
    //                                     $tbutton = $tbutton . "<button>" . $bval['text'] . "</button>";
    //                                 }
    //                         }
    //                     }
    //                 }

    //                 if (isset($send_array['template']['components'][0]['parameters'])) {
    //                     foreach ($send_array['template']['components'][0]['parameters'] as $key => $val) {
    //                         debug($key);
    //                         debug($val);
    //                         $key = $key + 1;
    //                         if($val['type']=="text"){
    //                             $tbody = str_replace('{{' . $key . '}}', $val['text'], $tbody);
    //                         }
    //                         if($val['type']=="image"){
    //                             $tbody = str_replace('{{' . $key . '}}',  '<img src="/campaigns/viewsendFile?fileid=' . $val['image']['id'].">', $tbody);
    //                         }
                            
    //                     }
    //                 }
    //                 $tbody = str_replace('\n', '<br>', $tbody);
    //                 $tbody = preg_replace('/(?:\*)([^*]*)(?:\*)/', '<strong>$1</strong>', $tbody);
    //                 $tbody = preg_replace('/(?:_)([^_]*)(?:_)/', '<i>$1</i>', $tbody);
    //                 $tbody = preg_replace('/(?:~)([^~]*)(?:~)/', '<strike>$1</strike>', $tbody);
    //                 $msg =  $theader . "<br>" . $tbody . "<br>" . $tbutton;
    //             } else {
    //                  $msg = "Missing template $template_name";
    //             }
    //             break;
    //         case "text":
    //             $msg = $send_array['text']['body'];
    //             break;
    //         case "interactive":
    //             $msg ="Interactive". $send_array['interactive']['body']['text'];
    //             break;    
    //         default:
    //             debug($send_array['type'])    ;
    //     }

    //     return $msg;
    // }


    // function arraytomsg($sendarray) {
    //     // debug($sendarray);
       
    //     $send_array = json_decode($this->_removeTrailingCommas($sendarray), true);
    //     // debug($send_array);
    //     $msg = null;
    //     switch ($send_array['type']) {
    //         case "template":
    //         case "api":
    //             $template_name = $send_array['template']['name'];
    //             // debug($template_name);
    //             $template_info = $this->getTableLocator()->get('Templates')->find()->where(['name' => $template_name])->toArray();
    
    //             // debug($template_info);
    //             if (!empty($template_info)) {
    //                 $template_details = json_decode($template_info[0]['template_details'], true);
    //                 // debug($template_details);
    //                 $tbutton = '';
    //                 $tbody = '';
    //                 $theader = '';
    //                 if (isset($template_details['data'])) {
    //                     // debug($template_details['data'][0]['components']);
    //                     foreach ($template_details['data'][0]['components'] as $key => $val) {
    //                         switch ($val['type']) {
    //                             case "HEADER":
    //                                 // debug($val);
    //                                 if ($val['format'] == "text") {
    //                                     $theader = "<b>" . $val['text'] . "</b>";
    //                                     // debug($val);
    //                                 } elseif ($val['format'] == "IMAGE") {
    //                                     // debug($val);
    //                                     $theader = '<img src="' . $val['example']['header_handle'][0] . '">';
    //                                 }
    //                                 break;
    //                             case "BODY":
    //                                 $tbody = $val['text'];
    //                                 break;
    //                             case "BUTTONS":
    //                                 foreach ($val['buttons'] as $bkey => $bval) {
    //                                     $tbutton .= "<button>" . $bval['text'] . "</button>";
    //                                 }
    //                                 break;
    //                         }
    //                     }
    //                 }
    
    //                 if (isset($send_array['template']['components'][0]['parameters'])) {
    //                     foreach ($send_array['template']['components'][0]['parameters'] as $key => $val) {
    //                         $key = $key + 1;
    //                         if ($val['type'] == "text") {
    //                             $tbody = str_replace('{{' . $key . '}}', $val['text'], $tbody);
    //                         }
    //                         if ($val['type'] == "image") {
    //                             $tbody = str_replace('{{' . $key . '}}', '<div class="image-container"><img src="/campaigns/viewsendFile?fileid=' . $val['image']['id'] . '"></div>', $tbody);
    //                         }
    //                     }
    //                 }
    //                 $tbody = str_replace('\n', '<br>', $tbody);
    //                 $tbody = preg_replace('/(?:\*)([^*]*)(?:\*)/', '<strong>$1</strong>', $tbody);
    //                 $tbody = preg_replace('/(?:_)([^_]*)(?:_)/', '<i>$1</i>', $tbody);
    //                 $tbody = preg_replace('/(?:~)([^~]*)(?:~)/', '<strike>$1</strike>', $tbody);
    //                 $msg = $theader . "<br>" . $tbody . "<br>" . $tbutton;
    //             } else {
    //                 $msg = "Missing template $template_name";
    //             }
    //             break;
    //         case "text":
    //             $msg = $send_array['text']['body'];
    //             break;
    //         case "interactive":
    //          //   debug($send_array);
    //             if(isset($send_array['interactive']['body']['text'])){
    //                 $msg = "Interactive " . $send_array['interactive']['body']['text'];
    //             }else{
    //                 $msg ="Interactive Menu"; 
    //             }
               
    //             break;
    //         case "request_welcome":
    //             $msg = "requestWelcome";   
    //             break;
    //         case "image":
    //            // debug($send_array);
    //             $msg= "<figure>";
    //             $msg =$msg.'<img src="/campaigns/viewsendFile?fileid=' . $send_array['image']['id'] . '">'; 
    //             if(isset($send_array['image']['caption'])){
    //                 $msg =$msg ."<figcaption>".$send_array['image']['caption']."</figcaption>";
    //             }
                
    //             $msg =$msg ."</figure>";
    //             break;    
    //         default:
    //             debug($send_array);
    //     }
    
    //     return $msg;
    // }
    

    function _removeTrailingCommas($json) {
        // Remove trailing commas before closing brackets
        $json = preg_replace('/,\s*([\]}])/m', '$1', $json);
        return $json;
    }
    

//    function index2() {
//        $this->viewBuilder()->setLayout('ajax');
//    }

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

}
