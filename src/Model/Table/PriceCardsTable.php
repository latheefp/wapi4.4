<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PriceCards Model
 *
 * @method \App\Model\Entity\PriceCard newEmptyEntity()
 * @method \App\Model\Entity\PriceCard newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\PriceCard[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PriceCard get($primaryKey, $options = [])
 * @method \App\Model\Entity\PriceCard findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\PriceCard patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PriceCard[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PriceCard|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PriceCard saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PriceCard[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PriceCard[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\PriceCard[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PriceCard[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PriceCardsTable extends Table
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

        $this->setTable('price_cards');
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
            ->scalar('country')
            ->maxLength('country', 128)
            ->requirePresence('country', 'create')
            ->notEmptyString('country');

        $validator
            ->scalar('country_code')
            ->maxLength('country_code', 11)
            ->requirePresence('country_code', 'create')
            ->notEmptyString('country_code');

        $validator
            ->numeric('marketing')
            ->requirePresence('marketing', 'create')
            ->notEmptyString('marketing');

        $validator
            ->numeric('utility')
            ->requirePresence('utility', 'create')
            ->notEmptyString('utility');

        $validator
            ->numeric('authentication')
            ->requirePresence('authentication', 'create')
            ->notEmptyString('authentication');

        $validator
            ->numeric('service')
            ->requirePresence('service', 'create')
            ->notEmptyString('service');

        $validator
            ->numeric('business_Initiated_rate')
            ->requirePresence('business_Initiated_rate', 'create')
            ->notEmptyString('business_Initiated_rate');

        $validator
            ->integer('authentication_international')
            ->allowEmptyString('authentication_international');

        $validator
            ->numeric('user_Initiated_rate')
            ->requirePresence('user_Initiated_rate', 'create')
            ->notEmptyString('user_Initiated_rate');

        $validator
            ->scalar('market')
            ->maxLength('market', 64)
            ->requirePresence('market', 'create')
            ->notEmptyString('market');

        return $validator;
    }
}
