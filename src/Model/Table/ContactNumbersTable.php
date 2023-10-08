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
 * @property \App\Model\Table\ContactsTable&\Cake\ORM\Association\BelongsTo $Contacts
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
class ContactNumbersTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('contact_numbers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Contacts', [
            'foreignKey' => 'contact_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsToMany('Contacts', [
            'foreignKey' => 'contact_number_id',
            'targetForeignKey' => 'contact_id',
            'joinTable' => 'contacts_contact_numbers',
        ]);

        $this->hasMany('ContactsContactNumbers', [
            'dependent' => true,
            'cascadeCallbacks' => true,
            'foreignKey' => 'contact_number_id',
            'targetForeignKey' => 'contact_id',
        ]);

//        $this->addBehavior('CounterCache', [
////        'Contacts' => ['contact_count' => [
////            'conditions' => ['matching' => ('ContactsContactNumbers', function (Query $q) use ($querydata)
////            {
////                return $q
////                ->where(['ContactsContactNumbers.contact_id' => $querydata['contact_id']])
////                });
////            ]
////        ]
////        ]
//        ]);

//        $this->addBehavior('CounterCache', [
//            'Contacts' => [
//                'whatsapp_count' => [
//                    'conditions' => [
//                        'matching'[
////                            'ContactsContactNumbers', function (Query $q) use ($querydata)
////                                        {
////                                        return $q
////                                        ->where(['ContactsContactNumbers.contact_id' => $querydata['contact_id']])
////                                        }
//                                       
//                                    ]
//        
//                    ]
//                ]
//            ]
//        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator {
        $validator
                ->integer('id')
                ->allowEmptyString('id', null, 'create');

        $validator
                ->scalar('mobile_number')
                ->maxLength('mobile_number', 12)
                ->requirePresence('mobile_number', 'create')
                ->notEmptyString('mobile_number');

//        $validator
//            ->scalar('name')
//            ->maxLength('name', 32)
//            ->allowEmptyString('name');
//        $validator
//            ->scalar('gender')
//            ->maxLength('gender', 1)
//            ->allowEmptyString('gender');
//        $validator
//            ->date('expiry')
//            ->allowEmptyDate('expiry');
//        $validator
//            ->boolean('whatsapp')
//            ->notEmptyString('whatsapp');
//
//        $validator
//            ->boolean('blocked')
//            ->notEmptyString('blocked');

        return $validator
                        ->add('mobile_number', [
                            'minLength' => [
                                'rule' => ['minLength', 12],
                                'message' => 'Mobile number should be 12 Digit including county code',
                            ]
                        ])
                        ->add('mobile_number', [
                            'maxLength' => [
                                'rule' => ['maxLength', 12],
                                'message' => 'Mobile number should be 12 Digit including county code',
                            ]
                        ])
                        ->add('mobile_number', [
                            'numeric' => [
                                'rule' => ['numeric'],
                                'message' => 'Mobile number should be numbers',
                            ]
                        ])

        ;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker {
        $rules->add($rules->existsIn(['contact_id'], 'Contacts'), ['errorField' => 'contact_id']);

        return $rules;
    }

}
