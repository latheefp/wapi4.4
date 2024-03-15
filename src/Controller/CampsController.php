<?php
declare(strict_types=1);

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
use Cake\Http\Exception\ForbiddenException;
use PhpParser\JsonDecoder;

/**
 * CampsController Controller
 *
 * @method \App\Model\Entity\CampsController[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */

 //This is public controllers to accept camps and process it. 
class CampsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $campsController = $this->paginate($this->CampsController);

        $this->set(compact('campsController'));
    }


    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['camps']);
    }


    function camps(){ //this function is maily for sendign data to default ticket function. 
        $this->viewBuilder()->setLayout('ajax');
        $queryParams = $this->getRequest()->getQueryParams();
        $cdata=$queryParams['cdata'];
        $cdataArray=json_decode(base64_decode($cdata),true);
     //   debug($cdataArray);

        //$cdataArray['action']="camps";



        $FBSettings=$this->_getFBsettings($cdataArray);
      //  debug($FBSettings);
        if($FBSettings['status']['code'] != 200){
            $this->response = $this->response->withStatus(500); // Unauthorized
            $response['error'] = 'Application error, No account found';
            $this->set('response', $response);
            return;
        }
     //   debug($FBSettings);
        //return false;

      //  $queryParams['action']="camps";
        $URL = $FBSettings['interactive_webhook'];
        $APIKEY = $FBSettings['interactive_api_key'];
     //   debug($URL);
        $URL="http://help_egrand/apis/wapi"; //temporary URL
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($cdataArray),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'APIKEY: ' . $APIKEY,
            //  'Cookie: CAKEPHP=rn8u792v5kqp6n3lic5m43ejvc'
            ),
        ));

        $responsejson = curl_exec($curl);
     //   debug($responsejson);
        // $this->writeinteractive($response, "Response json from Grand");
        $responsearray = json_decode($responsejson, true);
     //   debug($responsearray);

      //  $this->set('responsearray', $responsearray);

        $this->response = $this->response->withStatus(200); // Unauthorized
        $response['msg'] = $responsearray;
        $this->set('response', $response);
       // return;

    }


    function campskeyval(){ //this function is maily for sendign data to default ticket function. 
        $this->viewBuilder()->setLayout('ajax');
        $queryParams = $this->getRequest()->getQueryParams();
        $FBSettings=$this->_getFBsettings(['account_id'=>$queryParams['account_id']]);
      //  debug($FBSettings);
        if($FBSettings['status']['code'] != 200){
            $this->response = $this->response->withStatus(500); // Unauthorized
            $response['error'] = 'Application error, No account found';
            $this->set('response', $response);
            return;
        }
      //  debug($FBSettings);
        //return false;

        $queryParams['action']="camps";
        $URL = $FBSettings['interactive_webhook'];
        $APIKEY = $FBSettings['interactive_api_key'];
     //   debug($URL);
        $URL="http://help_egrand/apis/wapi"; //temporary URL
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($queryParams),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'APIKEY: ' . $APIKEY,
            //  'Cookie: CAKEPHP=rn8u792v5kqp6n3lic5m43ejvc'
            ),
        ));

        $responsejson = curl_exec($curl);
        // debug($responsejson);
        // $this->writeinteractive($response, "Response json from Grand");
        $responsearray = json_decode($responsejson, true);

      //  $this->set('responsearray', $responsearray);

        $this->response = $this->response->withStatus(200); // Unauthorized
        $response['msg'] = $responsearray;
        $this->set('response', $response);
       // return;

    }


    // function _processInteractive-delete($input, $FBSettings) {

    //     $postarray = json_decode(file_get_contents('php://input'), true);
    //     $this->writeinteractive($postarray, "input array ");
    //     $interactive = $postarray['entry'][0]['changes'][0]['value']['messages'][0]['interactive'];
    //     $wa_id = $postarray['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
    //     $repyId = $interactive['list_reply']['id'];
    //     $this->writeinteractive($repyId, "Reply ID");
    //     $query_str = parse_url($repyId, PHP_URL_QUERY);
    //     parse_str($repyId, $get_array);
    //     $this->writeinteractive($get_array, "Reply ID Array to send to Grand");

    //     $URL = $FBSettings['interactive_webhook'];
    //     $APIKEY = $FBSettings['interactive_api_key'];

    //     $curl = curl_init();

    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => $URL,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => json_encode($get_array),
    //         CURLOPT_HTTPHEADER => array(
    //             'Content-Type: application/json',
    //             'APIKEY: ' . $APIKEY,
    //         //  'Cookie: CAKEPHP=rn8u792v5kqp6n3lic5m43ejvc'
    //         ),
    //     ));

    //     $response = curl_exec($curl);
    //     $this->writeinteractive($response, "Response json from Grand");
    //     $responsearray = json_decode($response, true);
    //     $this->writeinteractive($responsearray, "Response Array from Grand");
    //     //  $notification_numbers=$this->_getAccountSettings('interactive_notification_numbers');
    //     curl_close($curl);
    //     $notification_numbers = (explode(',', $FBSettings['interactive_notification_numbers']));
    //     $notification_numbers[] = $wa_id;
    //     foreach ($notification_numbers as $key => $contact_number) {
    //         $this->_sendIntToCustomer($responsearray, $contact_number, $FBSettings);
    //     }
    // }

    /**
     * View method
     *
     * @param string|null $id Camps Controller id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $campsController = $this->CampsController->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('campsController'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $campsController = $this->CampsController->newEmptyEntity();
        if ($this->request->is('post')) {
            $campsController = $this->CampsController->patchEntity($campsController, $this->request->getData());
            if ($this->CampsController->save($campsController)) {
                $this->Flash->success(__('The camps controller has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The camps controller could not be saved. Please, try again.'));
        }
        $this->set(compact('campsController'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Camps Controller id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $campsController = $this->CampsController->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $campsController = $this->CampsController->patchEntity($campsController, $this->request->getData());
            if ($this->CampsController->save($campsController)) {
                $this->Flash->success(__('The camps controller has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The camps controller could not be saved. Please, try again.'));
        }
        $this->set(compact('campsController'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Camps Controller id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $campsController = $this->CampsController->get($id);
        if ($this->CampsController->delete($campsController)) {
            $this->Flash->success(__('The camps controller has been deleted.'));
        } else {
            $this->Flash->error(__('The camps controller could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
