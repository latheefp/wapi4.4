<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Apiviews Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Apiview newEmptyEntity()
 * @method \App\Model\Entity\Apiview newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Apiview[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Apiview get($primaryKey, $options = [])
 * @method \App\Model\Entity\Apiview findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Apiview patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Apiview[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Apiview|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Apiview saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Apiview[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Apiview[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Apiview[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Apiview[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ApiviewsTable extends Table
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

        $this->setTable('apiviews');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->notEmptyString('id');

        $validator
            ->scalar('api_name')
            ->maxLength('api_name', 32)
            ->requirePresence('api_name', 'create')
            ->notEmptyString('api_name');

        $validator
            ->scalar('api_key')
            ->maxLength('api_key', 64)
            ->allowEmptyString('api_key');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        $validator
            ->scalar('ip_list')
            ->maxLength('ip_list', 4294967295)
            ->requirePresence('ip_list', 'create')
            ->notEmptyString('ip_list');

        $validator
            ->scalar('username')
            ->maxLength('username', 255)
            ->allowEmptyString('username');

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
        $rules->add($rules->isUnique(['username']), ['errorField' => 'username']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
