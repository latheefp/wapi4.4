<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContactsSchedules Model
 *
 * @property \App\Model\Table\SchedulesTable&\Cake\ORM\Association\BelongsTo $Schedules
 *
 * @method \App\Model\Entity\ContactsSchedule newEmptyEntity()
 * @method \App\Model\Entity\ContactsSchedule newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ContactsSchedule[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContactsSchedule get($primaryKey, $options = [])
 * @method \App\Model\Entity\ContactsSchedule findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ContactsSchedule patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ContactsSchedule[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContactsSchedule|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactsSchedule saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactsSchedule[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactsSchedule[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactsSchedule[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactsSchedule[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContactsSchedulesTable extends Table
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

        $this->setTable('contacts_schedules');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Schedules', [
            'foreignKey' => 'schedule_id',
            'joinType' => 'INNER',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('contact_number')
            ->maxLength('contact_number', 12)
            ->requirePresence('contact_number', 'create')
            ->notEmptyString('contact_number');

        $validator
            ->scalar('lang')
            ->maxLength('lang', 16)
            ->notEmptyString('lang');

        $validator
            ->scalar('wamsgId')
            ->maxLength('wamsgId', 128)
            ->allowEmptyString('wamsgId');

        $validator
            ->dateTime('sent_time')
            ->allowEmptyDateTime('sent_time');

        $validator
            ->dateTime('read_time')
            ->allowEmptyDateTime('read_time');

        $validator
            ->dateTime('deliverd_time')
            ->allowEmptyDateTime('deliverd_time');

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
        $rules->add($rules->existsIn(['schedule_id'], 'Schedules'), ['errorField' => 'schedule_id']);

        return $rules;
    }
}
