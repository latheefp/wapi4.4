<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Ratings Model
 *
 * @property \App\Model\Table\StreamsTable&\Cake\ORM\Association\BelongsTo $Streams
 *
 * @method \App\Model\Entity\Rating newEmptyEntity()
 * @method \App\Model\Entity\Rating newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Rating[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Rating get($primaryKey, $options = [])
 * @method \App\Model\Entity\Rating findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Rating patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Rating[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Rating|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rating saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rating[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Rating[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Rating[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Rating[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RatingsTable extends Table
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

        $this->setTable('ratings');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Streams', [
            'foreignKey' => 'stream_id',
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
            ->numeric('old_balance')
            ->requirePresence('old_balance', 'create')
            ->notEmptyString('old_balance');

        $validator
            ->numeric('new_balance')
            ->requirePresence('new_balance', 'create')
            ->notEmptyString('new_balance');

        $validator
            ->numeric('cost')
            ->requirePresence('cost', 'create')
            ->notEmptyString('cost');

        $validator
            ->scalar('country')
            ->maxLength('country', 64)
            ->requirePresence('country', 'create')
            ->notEmptyString('country');

        $validator
            ->boolean('charging_status')
            ->notEmptyString('charging_status');

        $validator
            ->numeric('tax')
            ->notEmptyString('tax');

        $validator
            ->numeric('p_perc')
            ->notEmptyString('p_perc');

        $validator
            ->numeric('fb_cost')
            ->allowEmptyString('fb_cost');

        $validator
            ->scalar('conversation')
            ->maxLength('conversation', 64)
            ->allowEmptyString('conversation')
            ->add('conversation', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->numeric('rate_with_tax')
            ->notEmptyString('rate_with_tax');

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
        $rules->add($rules->isUnique(['conversation']), ['errorField' => 'conversation']);
        $rules->add($rules->existsIn(['stream_id'], 'Streams'), ['errorField' => 'stream_id']);

        return $rules;
    }
}
