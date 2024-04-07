<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContactNumbers Model
 *
 * @property \App\Model\Table\CampsTrackersTable&\Cake\ORM\Association\HasMany $CampsTrackers
 * @property \App\Model\Table\ContactsTable&\Cake\ORM\Association\BelongsToMany $Contacts
 *
 * @method \App\Model\Entity\ContactNumber newEmptyEntity()
 * @method \App\Model\Entity\ContactNumber newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ContactNumber[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContactNumber get($primaryKey, $options = [])
 * @method \App\Model\Entity\ContactNumber findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ContactNumber patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ContactNumber[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContactNumber|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactNumber saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactNumber[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactNumber[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactNumber[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactNumber[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ContactNumbersTable extends Table
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

        $this->setTable('contact_numbers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany(
            'CampsTrackers',
            [
                'foreignKey' => 'contact_number_id',
            ]
        );

        $this->hasMany(
            'ContactsContactNumbers',
            [
                'foreignKey' => 'contact_number_id',
            ]
        );
        $this->belongsToMany('Contacts', [
            'foreignKey' => 'contact_number_id',
            'targetForeignKey' => 'contact_id',
            'joinTable' => 'contacts_contact_numbers',
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
            ->scalar('mobile_number')
            ->maxLength('mobile_number', 12)
            ->requirePresence('mobile_number', 'create')
            ->notEmptyString('mobile_number');

        $validator
            ->scalar('name')
            ->maxLength('name', 32)
            ->allowEmptyString('name');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 1)
            ->allowEmptyString('gender');

        $validator
            ->date('expiry')
            ->allowEmptyDate('expiry');

        $validator
            ->boolean('whatsapp')
            ->notEmptyString('whatsapp');

        $validator
            ->boolean('blocked')
            ->notEmptyString('blocked');

        return $validator;
    }
}
