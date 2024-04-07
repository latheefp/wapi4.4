<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContactSchedules Model
 *
 * @property \App\Model\Table\SchedulesTable&\Cake\ORM\Association\BelongsTo $Schedules
 *
 * @method \App\Model\Entity\ContactSchedule newEmptyEntity()
 * @method \App\Model\Entity\ContactSchedule newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ContactSchedule[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContactSchedule get($primaryKey, $options = [])
 * @method \App\Model\Entity\ContactSchedule findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ContactSchedule patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ContactSchedule[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContactSchedule|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactSchedule saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactSchedule[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactSchedule[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactSchedule[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactSchedule[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ContactSchedulesTable extends Table
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

        $this->setTable('contact_schedules');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

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
            ->integer('schedule_id')
            ->notEmptyString('schedule_id');

        $validator
            ->scalar('contact_waid')
            ->maxLength('contact_waid', 15)
            ->requirePresence('contact_waid', 'create')
            ->notEmptyString('contact_waid');

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
        $rules->add($rules->existsIn('schedule_id', 'Schedules'), ['errorField' => 'schedule_id']);

        return $rules;
    }
}
