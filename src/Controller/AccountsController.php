<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Accounts Controller
 *
 * @property \App\Model\Table\AccountsTable $Accounts
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccountsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
         if( $this->getMyGID() !=1 ){
            $this->Flash->error(__('You do not have permission to edit this account.'));
            return $this->redirect(['action' => '/']);  
        }
        $accounts = $this->paginate($this->Accounts);

        $this->set(compact('accounts'));
    }

    /**
     * View method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
         if( $this->getMyGID() !=1 ){
            $this->Flash->error(__('You do not have permission to edit this account.'));
            return $this->redirect(['action' => '/']);  
        }
        $account = $this->Accounts->get($id, [
            'contain' => ['Users', 'ApiKeys', 'Apiviews', 'CampaignViews', 'Chats', 'ChatsSessions', 'Commands', 'ContactStreams', 'Contacts', 'InvoiceViews', 'Invoices', 'Permissions', 'RecentChats', 'ScheduleViews', 'Schedules', 'StreamViews', 'Streams', 'Templates', 'UgroupsPermissions'],
        ]);

        $this->set(compact('account'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
         if( $this->getMyGID() !=1 ){
            $this->Flash->error(__('You do not have permission to edit this account.'));
            return $this->redirect(['action' => '/']);  
        }
    
        $account = $this->Accounts->newEmptyEntity();
        if ($this->request->is('post')) {
            $account = $this->Accounts->patchEntity($account, $this->request->getData(),[
                'associated' => ['Countries'] // Ensure countries are saved correctly
            ]);
       //     debug($account);
            $account->user_id = $this->getMyUID(); // Assuming you have a method to get the current user's ID
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__('The account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account could not be saved. Please, try again.'));
        }
          $countries = $this->Accounts->Countries->find('list')->toArray();
        $this->set(compact('account','countries'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if( $this->getMyGID() !=1 ){
            $this->Flash->error(__('You do not have permission to edit this account.'));
            return $this->redirect(['action' => '/']);  
        }

        $account = $this->Accounts->get($id, [
             'contain' => ['Countries'] // Important to fetch associated countries
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $account = $this->Accounts->patchEntity($account, $this->request->getData(),[
                'associated' => ['Countries'] // Ensure countries are saved correctly
            ]);
            //debug($account);
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__('The account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account could not be saved. Please, try again.'));
        }
        $countries = $this->Accounts->Countries->find('list')->toArray();
        $this->set(compact('account', 'countries','countries'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
         if( $this->getMyGID() !=1 ){
            $this->Flash->error(__('You do not have permission to edit this account.'));
            return $this->redirect(['action' => '/']);  
        }
        $this->request->allowMethod(['post', 'delete']);
        $account = $this->Accounts->get($id);
        if ($this->Accounts->delete($account)) {
            $this->Flash->success(__('The account has been deleted.'));
        } else {
            $this->Flash->error(__('The account could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
