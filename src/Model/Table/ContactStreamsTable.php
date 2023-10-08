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
 * @property \App\Model\Table\StreamViewsTable&\Cake\ORM\Association\HasMany $StreamViews
 * @property \App\Model\Table\StreamsTable&\Cake\ORM\Association\HasMany $Streams
 * @property \App\Model\Table\Streams-25-janTable&\Cake\ORM\Association\HasMany $Streams-25-jan
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

        $this->hasMany('StreamViews', [
            'foreignKey' => 'contact_stream_id',
        ]);
        $this->hasMany('Streams', [
            'foreignKey' => 'contact_stream_id',
        ]);
        $this->hasMany('Streams-25-jan', [
            'foreignKey' => 'contact_stream_id',
        ]);
        $this->hasMany('Streams-feb-9', [
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('contact_number')
            ->maxLength('contact_number', 12)
            ->requirePresence('contact_number', 'create')
            ->notEmptyString('contact_number');

        $validator
            ->scalar('profile_name')
            ->maxLength('profile_name', 256)
            ->allowEmptyFile('profile_name');

        $validator
            ->scalar('name')
            ->maxLength('name', 256)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
