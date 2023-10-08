<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\HasMany $Accounts
 * @property \App\Model\Table\ApiKeysTable&\Cake\ORM\Association\HasMany $ApiKeys
 * @property \App\Model\Table\ApiviewsTable&\Cake\ORM\Association\HasMany $Apiviews
 * @property \App\Model\Table\CampaignViewsTable&\Cake\ORM\Association\HasMany $CampaignViews
 * @property \App\Model\Table\CampaignsTable&\Cake\ORM\Association\HasMany $Campaigns
 * @property \App\Model\Table\Campaigns-blockedTable&\Cake\ORM\Association\HasMany $Campaigns-blocked
 * @property \App\Model\Table\ContactsTable&\Cake\ORM\Association\HasMany $Contacts
 * @property \App\Model\Table\PointNotificationsTable&\Cake\ORM\Association\HasMany $PointNotifications
 * @property \App\Model\Table\ScheduleViewsTable&\Cake\ORM\Association\HasMany $ScheduleViews
 * @property \App\Model\Table\SchedulesTable&\Cake\ORM\Association\HasMany $Schedules
 * @property \App\Model\Table\UploadsTable&\Cake\ORM\Association\HasMany $Uploads
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Ugroups', [
            'foreignKey' => 'ugroup_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Accounts', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('ApiKeys', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Apiviews', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('CampaignViews', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Campaigns', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Campaigns-blocked', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('ContactNumbersViews', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Contacts', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('GroupsUsers', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('PointNotifications', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('ScheduleViews', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Schedules', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Uploads', [
            'foreignKey' => 'user_id',
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
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->dateTime('last_logged')
            ->allowEmptyDateTime('last_logged');

        $validator
            ->boolean('show_closed')
            ->notEmptyString('show_closed');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('username')
            ->maxLength('username', 255)
            ->requirePresence('username', 'create')
            ->notEmptyString('username')
            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->integer('active')
            ->allowEmptyString('active');

        $validator
            ->scalar('show_cols')
            ->allowEmptyString('show_cols');

        $validator
            ->integer('login_count')
            ->allowEmptyString('login_count');

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
        $rules->add($rules->isUnique(['username']), ['errorField' => 'username']);
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);
        $rules->add($rules->existsIn(['ugroup_id'], 'Ugroups'), ['errorField' => 'ugroup_id']);
        $rules->add($rules->existsIn(['account_id'], 'Accounts'), ['errorField' => 'account_id']);

        return $rules;
    }
}
