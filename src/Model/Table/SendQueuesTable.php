<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SendQueues Model
 *
 * @method \App\Model\Entity\SendQueue newEmptyEntity()
 * @method \App\Model\Entity\SendQueue newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SendQueue[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SendQueue get($primaryKey, $options = [])
 * @method \App\Model\Entity\SendQueue findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SendQueue patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SendQueue[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SendQueue|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SendQueue saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SendQueue[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SendQueue[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SendQueue[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SendQueue[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SendQueuesTable extends Table
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

        $this->setTable('send_queues');
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
            ->scalar('form_data')
            ->maxLength('form_data', 4294967295)
            ->requirePresence('form_data', 'create')
            ->notEmptyString('form_data');

        $validator
            ->scalar('status')
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        $validator
            ->boolean('processed')
            ->notEmptyString('processed');

        return $validator;
    }
}
