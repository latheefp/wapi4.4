<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RcvQueues Model
 *
 * @method \App\Model\Entity\RcvQueue newEmptyEntity()
 * @method \App\Model\Entity\RcvQueue newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\RcvQueue[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RcvQueue get($primaryKey, $options = [])
 * @method \App\Model\Entity\RcvQueue findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\RcvQueue patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RcvQueue[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RcvQueue|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RcvQueue saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RcvQueue[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RcvQueue[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\RcvQueue[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RcvQueue[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RcvQueuesTable extends Table
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

        $this->setTable('rcv_queues');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->scalar('status')
            ->maxLength('status', 512)
            ->allowEmptyString('status');

        $validator
            ->scalar('json')
            ->maxLength('json', 4294967295)
            ->allowEmptyString('json');

        $validator
            ->boolean('processed')
            ->allowEmptyString('processed');

        $validator
            ->dateTime('process_start_time')
            ->allowEmptyDateTime('process_start_time');

        $validator
            ->dateTime('process_end_time')
            ->allowEmptyDateTime('process_end_time');

        $validator
            ->scalar('http_response_code')
            ->maxLength('http_response_code', 3)
            ->notEmptyString('http_response_code');

        return $validator;
    }
}
