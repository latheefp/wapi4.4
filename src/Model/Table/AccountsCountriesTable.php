<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AccountsCountries Model
 *
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 * @property \App\Model\Table\CountriesTable&\Cake\ORM\Association\BelongsTo $Countries
 *
 * @method \App\Model\Entity\AccountsCountry newEmptyEntity()
 * @method \App\Model\Entity\AccountsCountry newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\AccountsCountry[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AccountsCountry get($primaryKey, $options = [])
 * @method \App\Model\Entity\AccountsCountry findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\AccountsCountry patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AccountsCountry[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\AccountsCountry|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AccountsCountry saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AccountsCountry[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AccountsCountry[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\AccountsCountry[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AccountsCountry[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AccountsCountriesTable extends Table
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

        $this->setTable('accounts_countries');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id',
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
            ->integer('account_id')
            ->notEmptyString('account_id');

        $validator
            ->integer('country_id')
            ->notEmptyString('country_id');

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
        $rules->add($rules->existsIn('account_id', 'Accounts'), ['errorField' => 'account_id']);
        $rules->add($rules->existsIn('country_id', 'Countries'), ['errorField' => 'country_id']);

        return $rules;
    }
}
