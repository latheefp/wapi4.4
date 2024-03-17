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
        $cdatabase64=$queryParams['cdata'];
        $cdataArray=json_decode(base64_decode($cdatabase64),true);
     //   debug($cdataArray);

        //validate hash.

        $camps_tracker_id=$cdataArray['camps_tracker_id'];
        $camps_trackerTable=$this->getTableLocator()->get('CampsTrackers');
        $camps_trackerrow=$camps_trackerTable->get($camps_tracker_id);
      //  debug($camps_trackerrow->hashvalue);
        if(!$this->validateSHAHash($cdatabase64, $camps_trackerrow->hashvalue) ){
            $this->response = $this->response->withStatus(403); // Unauthorized
            $response['error'] = 'Application error,  Validation failed';
            $this->set('response', $response);
            return;
        }
        //debug("Validated");

        //updating lead status. 
        $currentDateTime = new DateTime();
        $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');
        $camps_trackerrow->lead=true;
        $camps_trackerrow->leadtime=$formattedDateTime;
        $camps_trackerTable->save($camps_trackerrow);


     //   debug($camps_trackerrow);

        


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

      //TODO:consider backend option in cdata before sending to backend. 
        $URL = $FBSettings['interactive_webhook'];
        $APIKEY = $FBSettings['interactive_api_key'];
     //   debug($URL);
      #  $URL="http://help_egrand/apis/wapi"; //temporary URL
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
        return;

    }


    function campskeyval(){ //this function is maily for sendign data to default ticket function. can be deleted.
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
