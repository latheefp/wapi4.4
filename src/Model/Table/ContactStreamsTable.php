<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContactStreams Model
 *
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ChatsTable&\Cake\ORM\Association\HasMany $Chats
 * @property \App\Model\Table\RatingViewsTable&\Cake\ORM\Association\HasMany $RatingViews
 * @property \App\Model\Table\RecentChatsTable&\Cake\ORM\Association\HasMany $RecentChats
 * @property \App\Model\Table\StreamViewsTable&\Cake\ORM\Association\HasMany $StreamViews
 * @property \App\Model\Table\StreamsTable&\Cake\ORM\Association\HasMany $Streams
 *
 * @method \App\Model\Entity\ContactStream newEmptyEntity()
 * @method \App\Model\Entity\ContactStream newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ContactStream[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContactStream get($primaryKey, $options = [])
 * @method \App\Model\Entity\ContactStream findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ContactStream patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ContactStream[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContactStream|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactStream saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContactStream[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactStream[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactStream[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ContactStream[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContactStreamsTable extends Table
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

        $this->setTable('contact_streams');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Chats', [
            'foreignKey' => 'contact_stream_id',
        ]);
        $this->hasMany('RatingViews', [
            'foreignKey' => 'contact_stream_id',
        ]);
        $this->hasMany('RecentChats', [
            'foreignKey' => 'contact_stream_id',
        ]);
        $this->hasMany('StreamViews', [
            'foreignKey' => 'contact_stream_id',
        ]);
        $this->hasMany('Streams', [
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
            ->scalar('contact_number')
            ->maxLength('contact_number', 18)
            ->requirePresence('contact_number', 'create')
            ->notEmptyString('contact_number');

        $validator
            ->scalar('profile_name')
            ->maxLength('profile_name', 255)
            ->allowEmptyFile('profile_name');

        $validator
            ->integer('account_id')
            ->notEmptyString('account_id');

        $validator
            ->boolean('camp_blocked')
            ->notEmptyString('camp_blocked');

        $validator
            ->scalar('name')
            ->maxLength('name', 256)
            ->allowEmptyString('name');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

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
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
