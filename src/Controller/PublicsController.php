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
use Cake\Datasource\ConnectionManager;
use Cake\Http\Response;

/**
 * Apis Controller
 *
 * @method \App\Model\Entity\Api[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PublicsController extends AppController {

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
        $this->Authentication->allowUnauthenticated(['pricing', 'index', 'contact', 'healthcheck']);
    }

    public function index() {
        $this->viewBuilder()->setLayout('public');
    }

    //Configure::read(‘var_name’);

    function pricing() {
        $this->viewBuilder()->setLayout('public');
    }

    function contact() {
        $this->viewBuilder()->setLayout('public');
    }

    function healthcheck() {
        $this->viewBuilder()->setLayout('ajax');
        $connection = ConnectionManager::get('default');
        try {
            $connection->connect();
            $response = new Response();
            $response = $response->withType('application/json')
                    ->withStatus(200)
                    ->withStringBody(json_encode(['status' => 'OK']));
            return $response;
        } catch (\Cake\Database\Exception $e) {
            echo "Database is not available. Error: " . $e->getMessage();
            $response = new Response();
            $response = $response->withType('application/json')
                                 ->withStatus(500)
                                 ->withStringBody(json_encode(['status' => 'Database is not available', 'error' => $e->getMessage()]));

            return $response;
        }
    }
}
