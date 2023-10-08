<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pricings Model
 *
 * @property \App\Model\Table\StreamsTable&\Cake\ORM\Association\BelongsTo $Streams
 *
 * @method \App\Model\Entity\Pricing newEmptyEntity()
 * @method \App\Model\Entity\Pricing newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Pricing[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pricing get($primaryKey, $options = [])
 * @method \App\Model\Entity\Pricing findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Pricing patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pricing[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pricing|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pricing saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pricing[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pricing[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pricing[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pricing[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PricingsTable extends Table
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

        $this->setTable('pricings');
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
            ->scalar('category')
            ->maxLength('category', 58)
            ->requirePresence('category', 'create')
            ->notEmptyString('category');

        $validator
            ->boolean('billable')
            ->requirePresence('billable', 'create')
            ->notEmptyString('billable');

        $validator
            ->scalar('pricing_model')
            ->maxLength('pricing_model', 8)
            ->requirePresence('pricing_model', 'create')
            ->notEmptyString('pricing_model');

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
        $rules->add($rules->existsIn(['stream_id'], 'Streams'), ['errorField' => 'stream_id']);

        return $rules;
    }
}
