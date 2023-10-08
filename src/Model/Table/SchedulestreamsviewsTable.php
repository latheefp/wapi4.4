<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Schedulestreamsviews Model
 *
 * @property \App\Model\Table\SchedulesTable&\Cake\ORM\Association\BelongsTo $Schedules
 * @property \App\Model\Table\ContactsTable&\Cake\ORM\Association\BelongsTo $Contacts
 *
 * @method \App\Model\Entity\Schedulestreamsview newEmptyEntity()
 * @method \App\Model\Entity\Schedulestreamsview newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Schedulestreamsview get($primaryKey, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Schedulestreamsview|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedulestreamsview[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SchedulestreamsviewsTable extends Table
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

        $this->setTable('schedulestreamsviews');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Schedules', [
            'foreignKey' => 'schedule_id',
        ]);
        $this->belongsTo('Contacts', [
            'foreignKey' => 'contact_id',
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
            ->notEmptyString('id');

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
            ->scalar('contacts_profile_name')
            ->maxLength('contacts_profile_name', 32)
            ->allowEmptyFile('contacts_profile_name');

        $validator
            ->scalar('contact_waid')
            ->maxLength('contact_waid', 16)
            ->allowEmptyString('contact_waid');

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
        $rules->add($rules->existsIn(['schedule_id'], 'Schedules'), ['errorField' => 'schedule_id']);
        $rules->add($rules->existsIn(['contact_id'], 'Contacts'), ['errorField' => 'contact_id']);

        return $rules;
    }
}
