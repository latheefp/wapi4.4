<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Schedules Model
 *
 * @property \App\Model\Table\CampaignsTable&\Cake\ORM\Association\BelongsTo $Campaigns
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 * @property \App\Model\Table\SchedulestreamsviewsTable&\Cake\ORM\Association\HasMany $Schedulestreamsviews
 * @property \App\Model\Table\StreamViewsTable&\Cake\ORM\Association\HasMany $StreamViews
 * @property \App\Model\Table\StreamsTable&\Cake\ORM\Association\HasMany $Streams
 *
 * @method \App\Model\Entity\Schedule newEmptyEntity()
 * @method \App\Model\Entity\Schedule newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Schedule[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Schedule get($primaryKey, $options = [])
 * @method \App\Model\Entity\Schedule findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Schedule patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Schedule[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Schedule|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Schedule saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Schedule[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedule[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedule[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedule[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SchedulesTable extends Table
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

        $this->setTable('schedules');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Campaigns', [
            'foreignKey' => 'campaign_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Schedulestreamsviews', [
            'foreignKey' => 'schedule_id',
        ]);
        $this->hasMany('StreamViews', [
            'foreignKey' => 'schedule_id',
        ]);
        $this->hasMany('Streams', [
            'foreignKey' => 'schedule_id',
        ]);
        $this->belongsToMany('Contacts', [
            'foreignKey' => 'schedule_id',
            'targetForeignKey' => 'contact_id',
            'joinTable' => 'contacts_schedules',
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
            ->scalar('name')
            ->maxLength('name', 64)
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->integer('campaign_id')
            ->notEmptyString('campaign_id');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->integer('account_id')
            ->notEmptyString('account_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 32)
            ->allowEmptyString('status');

        $validator
            ->scalar('contact_csv')
            ->allowEmptyString('contact_csv');

        $validator
            ->scalar('http_response_code')
            ->maxLength('http_response_code', 4)
            ->notEmptyString('http_response_code');

        $validator
            ->integer('progress')
            ->allowEmptyString('progress');

        $validator
            ->integer('total_contact')
            ->notEmptyString('total_contact');

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
        $rules->add($rules->isUnique(['name']), ['errorField' => 'name']);
        $rules->add($rules->existsIn('campaign_id', 'Campaigns'), ['errorField' => 'campaign_id']);
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn('account_id', 'Accounts'), ['errorField' => 'account_id']);

        return $rules;
    }
}
