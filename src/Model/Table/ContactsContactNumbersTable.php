<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContactsContactNumbers Model
 *
 * @property \App\Model\Table\ContactNumbersTable&\Cake\ORM\Association\BelongsTo $ContactNumbers
 * @property \App\Model\Table\ContactsTable&\Cake\ORM\Association\BelongsTo $Contacts
 *
 * @method \App\Model\Entity\ContactsContactNumber newEmptyEntity()
 * @method \App\Model\Entity\ContactsContactNumber newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ContactsContactNumber[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContactsContactNumber get($primaryKey, $options = [])
 * @method \App\Model\Entity\ContactsContactNumber findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ContactsContactNumber patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ContactsContactNumber[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContactsContactNumber|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactsContactNumber saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactsContactNumber[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactsContactNumber[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactsContactNumber[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactsContactNumber[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ContactsContactNumbersTable extends Table
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

        $this->setTable('contacts_contact_numbers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ContactNumbers', [
            'foreignKey' => 'contact_number_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Contacts', [
            'foreignKey' => 'contact_id',
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
            ->integer('contact_number_id')
            ->notEmptyString('contact_number_id');

        $validator
            ->integer('contact_id')
            ->notEmptyString('contact_id');

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
        $rules->add($rules->existsIn('contact_number_id', 'ContactNumbers'), ['errorField' => 'contact_number_id']);
        $rules->add($rules->existsIn('contact_id', 'Contacts'), ['errorField' => 'contact_id']);

        return $rules;
    }
}
