<?php
//declare(strict_types=1);
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use MyApp\Chat;
class ChatController extends AppController {
    public function index() {
        // Create an instance of the Chat class
        $chat = new Chat();

        // You can now call methods of the Chat class
        $chat->onOpen(/* pass required parameters */);
        $chat->onMessage(/* pass required parameters */);
        // ...

        // Example: Send a message
        $chat->onMessage(/* pass required parameters */);

        // Return a response or render a view
        // ...
    }
}

