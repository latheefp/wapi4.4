<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Interactions Model
 *
 * @method \App\Model\Entity\Interaction newEmptyEntity()
 * @method \App\Model\Entity\Interaction newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Interaction[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Interaction get($primaryKey, $options = [])
 * @method \App\Model\Entity\Interaction findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Interaction patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Interaction[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Interaction|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Interaction saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Interaction[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Interaction[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Interaction[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Interaction[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class InteractionsTable extends Table
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

        $this->setTable('interactions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->scalar('api_action')
            ->maxLength('api_action', 32)
            ->allowEmptyString('api_action');

        $validator
            ->scalar('wa_action')
            ->maxLength('wa_action', 32)
            ->allowEmptyString('wa_action');

        return $validator;
    }
}
