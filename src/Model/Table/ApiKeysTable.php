<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApiKeys Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 *
 * @method \App\Model\Entity\ApiKey newEmptyEntity()
 * @method \App\Model\Entity\ApiKey newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ApiKey[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApiKey get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApiKey findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ApiKey patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApiKey[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApiKey|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApiKey saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApiKey[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ApiKey[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ApiKey[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ApiKey[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ApiKeysTable extends Table
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

        $this->setTable('api_keys');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
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
            ->scalar('api_name')
            ->maxLength('api_name', 32)
            ->requirePresence('api_name', 'create')
            ->notEmptyString('api_name')
            ->add('api_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('api_key')
            ->maxLength('api_key', 512)
            ->allowEmptyString('api_key');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        $validator
            ->scalar('ip_list')
            ->maxLength('ip_list', 15)
            ->allowEmptyString('ip_list');

        $validator
            ->integer('account_id')
            ->notEmptyString('account_id');

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
        $rules->add($rules->isUnique(['api_name']), ['errorField' => 'api_name']);
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn('account_id', 'Accounts'), ['errorField' => 'account_id']);

        return $rules;
    }
}
