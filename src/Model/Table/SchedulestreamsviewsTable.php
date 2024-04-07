<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Schedulestreamsviews Model
 *
 * @property \App\Model\Table\SchedulesTable&\Cake\ORM\Association\BelongsTo $Schedules
 *
 * @method \App\Model\Entity\Schedulestreamsview newEmptyEntity()
 * @method \App\Model\Entity\Schedulestreamsview newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Schedulestreamsview get($primaryKey, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Schedulestreamsview|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SchedulestreamsviewsTable extends Table
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

        $this->setTable('schedulestreamsviews');

        $this->belongsTo('Schedules', [
            'foreignKey' => 'schedule_id',
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
            ->scalar('lang')
            ->maxLength('lang', 8)
            ->notEmptyString('lang');

        $validator
            ->dateTime('sent_time')
            ->allowEmptyDateTime('sent_time');

        $validator
            ->dateTime('delivered_time')
            ->allowEmptyDateTime('delivered_time');

        $validator
            ->dateTime('read_time')
            ->allowEmptyDateTime('read_time');

        $validator
            ->boolean('has_wa')
            ->notEmptyString('has_wa');

        $validator
            ->integer('schedule_id')
            ->allowEmptyString('schedule_id');

        $validator
            ->scalar('contact_waid')
            ->maxLength('contact_waid', 18)
            ->allowEmptyString('contact_waid');

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
