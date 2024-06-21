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
use Cake\View\JsonView;
use Cake\View\XmlView;
use Cake\Http\Exception\UnauthorizedException;

//use App\Controller\Query;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class ReportsController extends AppController
{

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);

        $formaction = $this->request->getParam('action');

        $this->FormProtection->setConfig('unlockedActions', array(
            $formaction
        ));

       // $this->Authentication->allowUnauthenticated(['conversation_analytics']);
    }


    public function isAuthorized($user)
    {
        return true;
    }

    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    public function index()
    {

        $admin_id = $this->getMyGID();

        if ($admin_id == 1){

            $accountTable = $this->getTableLocator()->get('Accounts');
            $accounts = $accountTable->find()
            ->select(['id', 'company_name'])
            ->toList();
    
            $this->set('accounts', $accounts);
        }else{
            throw new UnauthorizedException('Unauthorized access');
        }


    }

    function conversationAnalytics()
    { 
        $admin_id = $this->getMyGID();

        if ($admin_id == 1){

      //  $this->viewBuilder()->setLayout('ajax');
        
        $formdata=$this->request->getData();

        $FBsettings=$this->_getFBsettings(['account_id'=>$formdata['account_id']]);


        $start_date_epoch = strtotime($formdata['start-date']);
        $end_date_epoch = strtotime($formdata['end-date']);

        $url = 'https://graph.facebook.com/v20.0/' . $FBsettings['WBAID'] .
            '?fields=conversation_analytics.start(' . $start_date_epoch .
            ').end(' . $end_date_epoch .
            ').granularity('. $formdata['type']. ').phone_numbers([]).dimensions([%22CONVERSATION_CATEGORY%22%2C%22CONVERSATION_TYPE%22%2C%22COUNTRY%22%2C%22PHONE%22])&access_token=' .
            $FBsettings['ACCESSTOKENVALUE'];


         //   echo ($url);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Cookie: ps_l=1; ps_n=1'
                ),
            ));
        $response = curl_exec($curl);
        curl_close($curl);
        $responseArray=json_decode($response,true);
      //  debug($response);
       // debug($responseArray);
        $this->set(compact('responseArray'));
        $this->viewBuilder()->setOption('serialize', 'responseArray');
        }else{
            throw new UnauthorizedException('Unauthorized access');
        }
    }
}
