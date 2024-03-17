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
 * @property \App\Model\Table\CampaignsTable&\Cake\ORM\Association\BelongsTo $Campaigns
 * @property \App\Model\Table\ContactNumbersTable&\Cake\ORM\Association\BelongsTo $ContactNumbers
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

        $this->belongsTo('Campaigns', [
            'foreignKey' => 'campaign_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ContactNumbers', [
            'foreignKey' => 'contact_number_id',
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
            ->integer('campaign_id')
            ->notEmptyString('campaign_id');

        $validator
            ->integer('contact_number_id')
            ->notEmptyString('contact_number_id');

        $validator
            ->boolean('lead')
            ->notEmptyString('lead');

        $validator
            ->dateTime('leadtime')
            ->allowEmptyDateTime('leadtime');

        $validator
            ->scalar('hashvalue')
            ->allowEmptyString('hashvalue');

        $validator
            ->integer('duplicate_blocked')
            ->notEmptyString('duplicate_blocked');

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
        $rules->add($rules->existsIn('campaign_id', 'Campaigns'), ['errorField' => 'campaign_id']);
        $rules->add($rules->existsIn('contact_number_id', 'ContactNumbers'), ['errorField' => 'contact_number_id']);

        return $rules;
    }
}
