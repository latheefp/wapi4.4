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

//use App\Controller\Query;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class InvoicesController extends AppController
{

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        //        $this->Security->setConfig('unlockedActions', ['getdata', 'edit']);
    }

    public function isAuthorized($user)
    {
        return true;
    }

    function invoinces(){
        $account_id = $this->getMyAccountID();
        $invoiceTable=$this->getTableLocator()->get("Invoices");
    }

    

   
}
