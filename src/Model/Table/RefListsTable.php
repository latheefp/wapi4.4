<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RefLists Model
 *
 * @method \App\Model\Entity\RefList newEmptyEntity()
 * @method \App\Model\Entity\RefList newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\RefList[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RefList get($primaryKey, $options = [])
 * @method \App\Model\Entity\RefList findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\RefList patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RefList[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RefList|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RefList saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RefList[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RefList[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\RefList[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RefList[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class RefListsTable extends Table
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

        $this->setTable('ref_lists');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
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
            ->scalar('token')
            ->maxLength('token', 16)
            ->requirePresence('token', 'create')
            ->notEmptyString('token');

        $validator
            ->scalar('table_name')
            ->maxLength('table_name', 64)
            ->requirePresence('table_name', 'create')
            ->notEmptyString('table_name');

        $validator
            ->scalar('function')
            ->maxLength('function', 32)
            ->requirePresence('function', 'create')
            ->notEmptyString('function');

        $validator
            ->scalar('field_name')
            ->maxLength('field_name', 64)
            ->requirePresence('field_name', 'create')
            ->notEmptyString('field_name');

        $validator
            ->scalar('comment')
            ->requirePresence('comment', 'create')
            ->notEmptyString('comment');

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
        $rules->add($rules->existsIn(['account_id'], 'Accounts'), ['errorField' => 'account_id']);

        return $rules;
    }
}
