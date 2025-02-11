<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Chats Model
 *
 * @property \App\Model\Table\ContactStreamsTable&\Cake\ORM\Association\BelongsTo $ContactStreams
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 * @property \App\Model\Table\StreamsTable&\Cake\ORM\Association\BelongsTo $Streams
 * @property \App\Model\Table\SessionsTable&\Cake\ORM\Association\BelongsToMany $Sessions
 *
 * @method \App\Model\Entity\Chat newEmptyEntity()
 * @method \App\Model\Entity\Chat newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Chat[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Chat get($primaryKey, $options = [])
 * @method \App\Model\Entity\Chat findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Chat patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Chat[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Chat|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Chat saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Chat[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Chat[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Chat[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Chat[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ChatsTable extends Table
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

        $this->setTable('chats');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ContactStreams', [
            'foreignKey' => 'contact_stream_id',
        ]);
        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
        ]);
        $this->belongsTo('Streams', [
            'foreignKey' => 'stream_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsToMany('Sessions', [
            'foreignKey' => 'chat_id',
            'targetForeignKey' => 'session_id',
            'joinTable' => 'chats_sessions',
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
            ->scalar('sendarray')
            ->maxLength('sendarray', 4294967295)
            ->allowEmptyString('sendarray');

        $validator
            ->scalar('recievearray')
            ->maxLength('recievearray', 4294967295)
            ->allowEmptyString('recievearray');

        $validator
            ->integer('contact_stream_id')
            ->allowEmptyString('contact_stream_id');

        $validator
            ->integer('account_id')
            ->allowEmptyString('account_id');

        $validator
            ->integer('stream_id')
            ->notEmptyString('stream_id');

        $validator
            ->scalar('type')
            ->maxLength('type', 32)
            ->allowEmptyString('type');

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
        $rules->add($rules->existsIn('contact_stream_id', 'ContactStreams'), ['errorField' => 'contact_stream_id']);
        $rules->add($rules->existsIn('account_id', 'Accounts'), ['errorField' => 'account_id']);
        $rules->add($rules->existsIn('stream_id', 'Streams'), ['errorField' => 'stream_id']);

        return $rules;
    }
}
