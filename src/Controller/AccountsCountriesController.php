<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * AccountsCountries Controller
 *
 * @property \App\Model\Table\AccountsCountriesTable $AccountsCountries
 * @method \App\Model\Entity\AccountsCountry[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccountsCountriesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Accounts', 'Countries'],
        ];
        $accountsCountries = $this->paginate($this->AccountsCountries);

        $this->set(compact('accountsCountries'));
    }

    /**
     * View method
     *
     * @param string|null $id Accounts Country id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $accountsCountry = $this->AccountsCountries->get($id, [
            'contain' => ['Accounts', 'Countries'],
        ]);

        $this->set(compact('accountsCountry'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $accountsCountry = $this->AccountsCountries->newEmptyEntity();
        if ($this->request->is('post')) {
            $accountsCountry = $this->AccountsCountries->patchEntity($accountsCountry, $this->request->getData());
            if ($this->AccountsCountries->save($accountsCountry)) {
                $this->Flash->success(__('The accounts country has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accounts country could not be saved. Please, try again.'));
        }
        $accounts = $this->AccountsCountries->Accounts->find('list', ['limit' => 200])->all();
        $countries = $this->AccountsCountries->Countries->find('list', ['limit' => 200])->all();
        $this->set(compact('accountsCountry', 'accounts', 'countries'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Accounts Country id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $accountsCountry = $this->AccountsCountries->get($id, [
            'contain' => ['Countries'] 
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $accountsCountry = $this->AccountsCountries->patchEntity($accountsCountry, $this->request->getData());
            if ($this->AccountsCountries->save($accountsCountry)) {
                $this->Flash->success(__('The accounts country has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accounts country could not be saved. Please, try again.'));
        }
 
        $accounts = $this->AccountsCountries->Accounts->find('list', ['limit' => 200])->all();
        $countries = $this->AccountsCountries->Countries->find('list', ['limit' => 200])->all();
        $this->set(compact('accountsCountry', 'accounts', 'countries'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Accounts Country id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $accountsCountry = $this->AccountsCountries->get($id);
        if ($this->AccountsCountries->delete($accountsCountry)) {
            $this->Flash->success(__('The accounts country has been deleted.'));
        } else {
            $this->Flash->error(__('The accounts country could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
