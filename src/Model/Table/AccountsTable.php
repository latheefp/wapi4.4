<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Accounts Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ApiKeysTable&\Cake\ORM\Association\HasMany $ApiKeys
 * @property \App\Model\Table\ApiviewsTable&\Cake\ORM\Association\HasMany $Apiviews
 * @property \App\Model\Table\CampaignViewsTable&\Cake\ORM\Association\HasMany $CampaignViews
 * @property \App\Model\Table\ChatsTable&\Cake\ORM\Association\HasMany $Chats
 * @property \App\Model\Table\ChatsSessionsTable&\Cake\ORM\Association\HasMany $ChatsSessions
 * @property \App\Model\Table\CommandsTable&\Cake\ORM\Association\HasMany $Commands
 * @property \App\Model\Table\ContactStreamsTable&\Cake\ORM\Association\HasMany $ContactStreams
 * @property \App\Model\Table\ContactsTable&\Cake\ORM\Association\HasMany $Contacts
 * @property \App\Model\Table\InvoiceViewsTable&\Cake\ORM\Association\HasMany $InvoiceViews
 * @property \App\Model\Table\InvoicesTable&\Cake\ORM\Association\HasMany $Invoices
 * @property \App\Model\Table\PermissionsTable&\Cake\ORM\Association\HasMany $Permissions
 * @property \App\Model\Table\RecentChatsTable&\Cake\ORM\Association\HasMany $RecentChats
 * @property \App\Model\Table\ScheduleViewsTable&\Cake\ORM\Association\HasMany $ScheduleViews
 * @property \App\Model\Table\SchedulesTable&\Cake\ORM\Association\HasMany $Schedules
 * @property \App\Model\Table\StreamViewsTable&\Cake\ORM\Association\HasMany $StreamViews
 * @property \App\Model\Table\StreamsTable&\Cake\ORM\Association\HasMany $Streams
 * @property \App\Model\Table\TemplatesTable&\Cake\ORM\Association\HasMany $Templates
 * @property \App\Model\Table\UgroupsPermissionsTable&\Cake\ORM\Association\HasMany $UgroupsPermissions
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Account newEmptyEntity()
 * @method \App\Model\Entity\Account newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Account[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Account get($primaryKey, $options = [])
 * @method \App\Model\Entity\Account findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Account patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Account[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Account|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Account saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AccountsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('accounts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ApiKeys', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Apiviews', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('CampaignViews', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Chats', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('ChatsSessions', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Commands', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('ContactStreams', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Contacts', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('InvoiceViews', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Invoices', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Permissions', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('RecentChats', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('ScheduleViews', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Schedules', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('StreamViews', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Streams', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Templates', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('UgroupsPermissions', [
            'foreignKey' => 'account_id',
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'account_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('company_name')
            ->maxLength('company_name', 32)
            ->requirePresence('company_name', 'create')
            ->notEmptyString('company_name');

        $validator
            ->scalar('Address')
            ->requirePresence('Address', 'create')
            ->notEmptyString('Address');

        $validator
            ->scalar('primary_contact_person')
            ->maxLength('primary_contact_person', 20)
            ->requirePresence('primary_contact_person', 'create')
            ->notEmptyString('primary_contact_person');

        $validator
            ->scalar('primary_number')
            ->maxLength('primary_number', 14)
            ->requirePresence('primary_number', 'create')
            ->notEmptyString('primary_number');

        $validator
            ->scalar('secondary_number')
            ->maxLength('secondary_number', 14)
            ->allowEmptyString('secondary_number');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->numeric('current_balance')
            ->requirePresence('current_balance', 'create')
            ->notEmptyString('current_balance');

        $validator
            ->scalar('WBAID')
            ->maxLength('WBAID', 32)
            ->requirePresence('WBAID', 'create')
            ->notEmptyString('WBAID');

        $validator
            ->scalar('API_VERSION')
            ->maxLength('API_VERSION', 5)
            ->requirePresence('API_VERSION', 'create')
            ->notEmptyString('API_VERSION');

        $validator
            ->scalar('ACCESSTOKENVALUE')
            ->maxLength('ACCESSTOKENVALUE', 256)
            ->requirePresence('ACCESSTOKENVALUE', 'create')
            ->notEmptyString('ACCESSTOKENVALUE');

        $validator
            ->scalar('phone_numberId')
            ->maxLength('phone_numberId', 32)
            ->requirePresence('phone_numberId', 'create')
            ->notEmptyString('phone_numberId');

        $validator
            ->scalar('def_language')
            ->maxLength('def_language', 12)
            ->requirePresence('def_language', 'create')
            ->notEmptyString('def_language');

        $validator
            ->scalar('test_number')
            ->maxLength('test_number', 14)
            ->requirePresence('test_number', 'create')
            ->notEmptyString('test_number');

        $validator
            ->time('restricted_start_time')
            ->requirePresence('restricted_start_time', 'create')
            ->notEmptyTime('restricted_start_time');

        $validator
            ->time('restricted_end_time')
            ->requirePresence('restricted_end_time', 'create')
            ->notEmptyTime('restricted_end_time');

        $validator
            ->scalar('interactive_webhook')
            ->maxLength('interactive_webhook', 128)
            ->requirePresence('interactive_webhook', 'create')
            ->notEmptyString('interactive_webhook');

        $validator
            ->scalar('rcv_notification_template')
            ->maxLength('rcv_notification_template', 32)
            ->allowEmptyString('rcv_notification_template');

        $validator
            ->scalar('interactive_api_key')
            ->maxLength('interactive_api_key', 256)
            ->requirePresence('interactive_api_key', 'create')
            ->notEmptyString('interactive_api_key');

        $validator
            ->scalar('interactive_menu_function')
            ->maxLength('interactive_menu_function', 64)
            ->allowEmptyString('interactive_menu_function');

        $validator
            ->scalar('interactive_notification_numbers')
            ->maxLength('interactive_notification_numbers', 14)
            ->requirePresence('interactive_notification_numbers', 'create')
            ->notEmptyString('interactive_notification_numbers');

        $validator
            ->scalar('def_isd')
            ->maxLength('def_isd', 6)
            ->requirePresence('def_isd', 'create')
            ->notEmptyString('def_isd');

        $validator
            ->scalar('welcome_msg')
            ->allowEmptyString('welcome_msg');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
