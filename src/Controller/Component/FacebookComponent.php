<?php
// src/Controller/Component/FacebookComponent.php
namespace App\Controller\Component;
#use Cake\Http\Session\DatabaseSession;
#use Cake\Datasource\ConnectionManager;
use Cake\Controller\Component;
use Cake\Http\Exception\BadRequestException;
#use Cake\ORM\Locator\TableLocator;
use Cake\ORM\Locator\LocatorAwareTrait;
#use Cake\ORM\TableRegistry;


class FacebookComponent extends Component
{
    use LocatorAwareTrait;
    public function getFBSettings($account_id, $user_id)
    {
        // You can move your _getFBsettings() logic here if needed
        return $this->_registry->getController()->_getFBsettings([
            'account_id' => $account_id,
            'user_id' => $user_id
        ]);
    }

    public function checkEligibility($account_id, $user_id, $mobile_number)
    {

        $FBSettings = $this->_getFBsettings(['account_id' => $account_id, 'user_id' => $user_id]);
        $country = $this->_getCountry($mobile_number, $FBSettings);

        if (in_array((int) $country->country_code, $FBSettings['country_ids'])) {
            if ($FBSettings['Balance'] < 1) {
                return [
                    'status' => 'failed',
                    'countries' => json_encode($FBSettings['country_ids']),
                    'requested_country' => $country->country,
                    'balance' => $FBSettings['Balance'],
                    'message' => 'Not enough balance to send message',
                    'version' => '1.0'
                ];
            } else {
                return [
                    'status' => 'success',
                    'countries' => json_encode($FBSettings['country_ids']),
                    'requested_country' => $country->country,
                    'version' => '1.0'
                ];
            }
        } else {
            throw new BadRequestException("Country {$country->country} is not eligible for this account");
        }
    }



    public function _getFBsettings($data)
{
   // debug("Facbook");
    $result = null;

    if (!empty($data['api_key'])) {
        $api = $this->getTableLocator()->get('ApiKeys')->find()
            ->where(['api_key' => $data['api_key'], 'enabled' => true])
            ->first();

        if (empty($api)) {
            return $this->_fbError("Wrong API Key", 404, $data);
        }

        $result = $this->getTableLocator()->get('Accounts')
            ->find()
            ->contain(['Countries' => function ($q) {
                return $q->select(['Countries.phonecode']);
            }])
            ->where(['Accounts.id' => $api->account_id])
            ->first();
    } elseif (!empty($data['user_id'])) {
        $user = $this->getTableLocator()->get('Users')->find()
            ->where(['id' => $data['user_id']])
            ->first();

        if (empty($user)) {
            return $this->_fbError("Wrong user info", 404, $data);
        }

        $result = $this->getTableLocator()->get('Accounts')
            ->find()
            ->contain(['Countries' => function ($q) {
                return $q->select(['Countries.phonecode']);
            }])
            ->where(['Accounts.id' => $user->account_id])
            ->first();
    } elseif (!empty($data['phone_numberId'])) {
        $result = $this->getTableLocator()->get('Accounts')
            ->find()
            ->contain(['Countries' => function ($q) {
                return $q->select(['Countries.phonecode']);
            }])
            ->where(['phone_numberId' => $data['phone_numberId']])
            ->first();
    } elseif (!empty($data['account_id'])) {
        $result = $this->getTableLocator()->get('Accounts')
            ->find()
            ->contain(['Countries' => function ($q) {
                return $q->select(['Countries.phonecode']);
            }])
            ->where(['id' => $data['account_id']])
            ->first();
    }

    if (empty($result)) {
        return $this->_fbError("No related account info found.", 403, $data);
    }

    $data += [
        'WBAID' => $result->WBAID,
        'Balance' => $result->current_balance,
        'API_VERSION' => $result->API_VERSION,
        'phone_numberId' => $result->phone_numberId,
        'def_language' => $result->def_language,
        'test_number' => $result->test_number,
        'def_isd' => $result->def_isd,
        'interactive_webhook' => $result->interactive_webhook,
        'interactive_notification_numbers' => $result->interactive_notification_numbers,
        'interactive_api_key' => $result->interactive_api_key,
        'rcv_notification_template' => $result->rcv_notification_template,
        'welcome_msg' => $result->welcome_msg,
        'interactive_menu_function' => $result->interactive_menu_function,
        'account_id' => $result->id,
        'ACCESSTOKENVALUE' => (intval(getenv('SEND_MSG')) === 1)
            ? $result->ACCESSTOKENVALUE
            : "Message not enabled, current value is " . intval(getenv('SEND_MSG')),
    ];

    // ✅ Add only country IDs
    if (!empty($result->countries)) {
        $data['country_ids'] = collection($result->countries)->extract('phonecode')->toList();
    } else {
        $data['country_ids'] = [];
    }

    $data['status'] = ['type' => 'Success', 'code' => 200];

    return $data;
}

private function _fbError($msg, $code, $data)
{
    $data['status'] = [
        'type' => 'Error',
        'message' => $msg,
        'code' => $code
    ];
    return $data;
}


function _getCountry($ph = null)
    {
        //   debug($contact);
        //    $ph = "972345449595050";
        if (strlen($ph) === 12) {
        }
        //$this->_format_mobile($ph, $data)
        $Country = [];

        $this->writelog($ph, "phone number");

        $pricaTable = $this->getTableLocator()->get('PriceCards');
        $codes = $pricaTable->find()
            ->order(['country_code DESC'])
            ->all();

        foreach ($codes as $key => $val) {
            $this->writelog($val, "Current code array");
            if (substr($ph, 0, strlen((string) $val->country_code)) == $val->country_code) {
                $Country = $val;
                break;
            }
        }
        return $Country;

        //  debug($Country);
    }


     function writelog($data, $type = null)
    {
        //        if (!$this->_getsettings("log_enabled")) {
        //            return false;
        //        }
        //   print (getenv('LOG'));
        if (intval(getenv('LOG')) == false) {

            //   debug("No logs");
            return false;
        }
        // debug("Logs are enabled");
        $file = LOGS . 'GrandWA' . '.log';
        #  $data =json_encode($event)."\n";  
        $time = date("Y-m-d H:i:s", time());
        $handle = fopen($file, 'a') or die('Cannot open file:  ' . $file); //implicitly creates file
        fwrite($handle, print_r("\n========================$type : $time============================= \n", true));
        fwrite($handle, print_r($data, true));
        fclose($handle);
    }

}
