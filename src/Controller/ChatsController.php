<?php

namespace App\Controller;

use App\Controller\AppController;
//use MyApp\Chat; // Import the Chat class
use Cake\Utility\Hash;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use Cake\Event\Event;
use DateTime;
use Cake\Mailer\Mailer;

class ChatsController extends AppController {

    var $uses = array('Campaigns');

    public function isAuthorized($user) {
        return true;
    }

    public function beforeFilter(EventInterface $event): void {
        parent::beforeFilter($event);
        $this->Security->setConfig('unlockedActions', ['getdata', 'edit']);
    }

    function index() {
        $chat = new Chat();
//
//        // You can now call methods of the Chat class
//        $chat->onOpen(/* pass required parameters */);
//        $chat->onMessage(/* pass required parameters */);
//        // ...
//        // Example: Send a message
//        $chat->onMessage(/* pass required parameters */);
    }
    
    

}
