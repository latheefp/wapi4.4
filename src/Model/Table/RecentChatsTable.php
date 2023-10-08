<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RecentChats Model
 *
 * @property \App\Model\Table\ContactStreamsTable&\Cake\ORM\Association\BelongsTo $ContactStreams
 *
 * @method \App\Model\Entity\RecentChat newEmptyEntity()
 * @method \App\Model\Entity\RecentChat newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\RecentChat[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RecentChat get($primaryKey, $options = [])
 * @method \App\Model\Entity\RecentChat findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\RecentChat patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RecentChat[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RecentChat|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RecentChat saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RecentChat[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RecentChat[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\RecentChat[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RecentChat[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class RecentChatsTable extends Table
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

        $this->setTable('recent_chats');
        $this->setDisplayField('name');

        $this->belongsTo('ContactStreams', [
            'foreignKey' => 'contact_stream_id',
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
            ->allowEmptyString('id');

        $validator
            ->scalar('contact_number')
            ->maxLength('contact_number', 12)
            ->allowEmptyString('contact_number');

        $validator
            ->scalar('name')
            ->maxLength('name', 256)
            ->allowEmptyString('name');

        $validator
            ->scalar('profile_name')
            ->maxLength('profile_name', 256)
            ->allowEmptyFile('profile_name');

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
        $rules->add($rules->existsIn(['contact_stream_id'], 'ContactStreams'), ['errorField' => 'contact_stream_id']);

        return $rules;
    }
}
