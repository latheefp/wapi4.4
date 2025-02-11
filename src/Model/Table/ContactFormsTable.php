<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Contactforms Model
 *
 * @method \App\Model\Entity\Contactform newEmptyEntity()
 * @method \App\Model\Entity\Contactform newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Contactform[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Contactform get($primaryKey, $options = [])
 * @method \App\Model\Entity\Contactform findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Contactform patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Contactform[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Contactform|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Contactform saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Contactform[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Contactform[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Contactform[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Contactform[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContactformsTable extends Table
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

        $this->setTable('contactforms');
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
            ->scalar('first_name')
            ->maxLength('first_name', 18)
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name');

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 18)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('company')
            ->maxLength('company', 32)
            ->requirePresence('company', 'create')
            ->notEmptyString('company');

        $validator
            ->scalar('contact_number')
            ->maxLength('contact_number', 20)
            ->requirePresence('contact_number', 'create')
            ->notEmptyString('contact_number');

        $validator
            ->integer('msg_per_month')
            ->requirePresence('msg_per_month', 'create')
            ->notEmptyString('msg_per_month');

        $validator
            ->integer('msg_per_day')
            ->requirePresence('msg_per_day', 'create')
            ->notEmptyString('msg_per_day');

        $validator
            ->scalar('target_country')
            ->maxLength('target_country', 32)
            ->requirePresence('target_country', 'create')
            ->notEmptyString('target_country');

        $validator
            ->scalar('details')
            ->allowEmptyString('details');

        return $validator;
    }
}
