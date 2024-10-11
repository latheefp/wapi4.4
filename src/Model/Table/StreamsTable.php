<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Streams Model
 *
 * @property \App\Model\Table\ContactStreamsTable&\Cake\ORM\Association\BelongsTo $ContactStreams
 * @property \App\Model\Table\SchedulesTable&\Cake\ORM\Association\BelongsTo $Schedules
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 * @property \App\Model\Table\RatingViewsTable&\Cake\ORM\Association\HasMany $RatingViews
 * @property \App\Model\Table\RatingsTable&\Cake\ORM\Association\HasMany $Ratings
 * @property \App\Model\Table\Ratings-aug-30Table&\Cake\ORM\Association\HasMany $Ratings-aug-30
 * @property \App\Model\Table\StreamsUpdatesTable&\Cake\ORM\Association\HasMany $StreamsUpdates
 *
 * @method \App\Model\Entity\Stream newEmptyEntity()
 * @method \App\Model\Entity\Stream newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Stream[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Stream get($primaryKey, $options = [])
 * @method \App\Model\Entity\Stream findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Stream patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Stream[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Stream|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Stream saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Stream[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Stream[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Stream[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Stream[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StreamsTable extends Table
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

        $this->setTable('streams');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('StreamToChat');
        $this->belongsTo('ContactStreams', [
            'foreignKey' => 'contact_stream_id',
        ]);
        $this->belongsTo('Schedules', [
            'foreignKey' => 'schedule_id',
        ]);
        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Chats', [
            'foreignKey' => 'stream_id',
        ]);
        $this->hasMany('RatingViews', [
            'foreignKey' => 'stream_id',
        ]);
        $this->hasMany('Ratings', [
            'foreignKey' => 'stream_id',
        ]);
        $this->hasMany('Ratings-aug-30', [
            'foreignKey' => 'stream_id',
        ]);
        $this->hasMany('StreamsUpdates', [
            'foreignKey' => 'stream_id',
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
            ->scalar('hookid')
            ->maxLength('hookid', 32)
            ->allowEmptyString('hookid');

        $validator
            ->scalar('messaging_product')
            ->maxLength('messaging_product', 32)
            ->allowEmptyString('messaging_product');

        $validator
            ->scalar('display_phone_number')
            ->maxLength('display_phone_number', 32)
            ->allowEmptyString('display_phone_number');

        $validator
            ->scalar('phonenumberid')
            ->maxLength('phonenumberid', 32)
            ->allowEmptyString('phonenumberid');

        $validator
            ->integer('contact_stream_id')
            ->allowEmptyString('contact_stream_id');

        $validator
            ->integer('schedule_id')
            ->allowEmptyString('schedule_id');

        $validator
            ->scalar('lang')
            ->maxLength('lang', 8)
            ->notEmptyString('lang');

        $validator
            ->scalar('message_context_from')
            ->maxLength('message_context_from', 32)
            ->allowEmptyString('message_context_from');

        $validator
            ->scalar('message_from')
            ->maxLength('message_from', 32)
            ->allowEmptyString('message_from');

        $validator
            ->dateTime('message_timestamp')
            ->allowEmptyDateTime('message_timestamp');

        $validator
            ->scalar('message_txt_body')
            ->allowEmptyString('message_txt_body');

        $validator
            ->scalar('message_context')
            ->maxLength('message_context', 16)
            ->allowEmptyString('message_context');

        $validator
            ->scalar('message_contextId')
            ->maxLength('message_contextId', 128)
            ->allowEmptyString('message_contextId');

        $validator
            ->scalar('message_contextFrom')
            ->maxLength('message_contextFrom', 16)
            ->allowEmptyString('message_contextFrom');

        $validator
            ->scalar('messageid')
            ->maxLength('messageid', 512)
            ->allowEmptyString('messageid');

        $validator
            ->scalar('initiator')
            ->maxLength('initiator', 12)
            ->notEmptyString('initiator');

        $validator
            ->scalar('replyid')
            ->maxLength('replyid', 128)
            ->allowEmptyString('replyid');

        $validator
            ->dateTime('timestamp')
            ->allowEmptyDateTime('timestamp');

        $validator
            ->scalar('type')
            ->maxLength('type', 32)
            ->allowEmptyString('type');

        $validator
            ->boolean('has_wa')
            ->notEmptyString('has_wa');

        $validator
            ->scalar('message_format_type')
            ->maxLength('message_format_type', 64)
            ->allowEmptyString('message_format_type');

        $validator
            ->dateTime('read_time')
            ->allowEmptyDateTime('read_time');

        $validator
            ->dateTime('delivered_time')
            ->allowEmptyDateTime('delivered_time');

        $validator
            ->dateTime('sent_time')
            ->allowEmptyDateTime('sent_time');

        $validator
            ->scalar('button_payload')
            ->maxLength('button_payload', 32)
            ->allowEmptyString('button_payload');

        $validator
            ->scalar('button_text')
            ->maxLength('button_text', 32)
            ->allowEmptyString('button_text');

        $validator
            ->scalar('sendarray')
            ->maxLength('sendarray', 4294967295)
            ->allowEmptyString('sendarray');

        $validator
            ->scalar('postdata')
            ->allowEmptyString('postdata');

        $validator
            ->scalar('recievearray')
            ->maxLength('recievearray', 4294967295)
            ->allowEmptyString('recievearray');

        $validator
            ->scalar('result')
            ->maxLength('result', 4294967295)
            ->allowEmptyString('result');

        $validator
            ->boolean('billable')
            ->allowEmptyString('billable');

        $validator
            ->scalar('pricing_model')
            ->maxLength('pricing_model', 64)
            ->allowEmptyString('pricing_model');

        $validator
            ->numeric('costed')
            ->allowEmptyString('costed');

        $validator
            ->boolean('rated')
            ->notEmptyString('rated');

        $validator
            ->scalar('category')
            ->maxLength('category', 64)
            ->allowEmptyString('category');

        $validator
            ->boolean('success')
            ->allowEmptyString('success');

        $validator
            ->scalar('errors')
            ->allowEmptyString('errors');

        $validator
            ->boolean('commented')
            ->notEmptyString('commented');

        $validator
            ->scalar('conversationid')
            ->maxLength('conversationid', 64)
            ->allowEmptyString('conversationid');

        $validator
            ->integer('account_id')
            ->notEmptyString('account_id');

        $validator
            ->dateTime('conversation_expiration_timestamp')
            ->allowEmptyDateTime('conversation_expiration_timestamp');

        $validator
            ->scalar('conversation_origin_type')
            ->maxLength('conversation_origin_type', 64)
            ->allowEmptyString('conversation_origin_type');

        $validator
            ->scalar('tmp_upate_json')
            ->allowEmptyString('tmp_upate_json');

        $validator
            ->boolean('live_chat_notified')
            ->notEmptyString('live_chat_notified');

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
        $rules->add($rules->existsIn('schedule_id', 'Schedules'), ['errorField' => 'schedule_id']);
        $rules->add($rules->existsIn('account_id', 'Accounts'), ['errorField' => 'account_id']);

        return $rules;
    }
}
