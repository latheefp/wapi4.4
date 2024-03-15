<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CampsTrackers Model
 *
 * @property \App\Model\Table\ContactStreamsTable&\Cake\ORM\Association\BelongsTo $ContactStreams
 *
 * @method \App\Model\Entity\CampsTracker newEmptyEntity()
 * @method \App\Model\Entity\CampsTracker newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CampsTracker[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CampsTracker get($primaryKey, $options = [])
 * @method \App\Model\Entity\CampsTracker findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CampsTracker patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CampsTracker[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CampsTracker|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CampsTracker saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CampsTracker[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CampsTracker[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CampsTracker[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CampsTracker[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CampsTrackersTable extends Table
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

        $this->setTable('camps_trackers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ContactStreams', [
            'foreignKey' => 'contact_stream_id',
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
            ->integer('campain_id')
            ->requirePresence('campain_id', 'create')
            ->notEmptyString('campain_id');

        $validator
            ->integer('contact_stream_id')
            ->notEmptyString('contact_stream_id');

        $validator
            ->boolean('lead')
            ->notEmptyString('lead');

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

        return $rules;
    }
}
