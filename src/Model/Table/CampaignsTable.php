<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Campaigns Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\TemplatesTable&\Cake\ORM\Association\BelongsTo $Templates
 * @property \App\Model\Table\CampaignFormsTable&\Cake\ORM\Association\HasMany $CampaignForms
 * @property \App\Model\Table\CampsTrackersTable&\Cake\ORM\Association\HasMany $CampsTrackers
 * @property \App\Model\Table\ScheduleViewsTable&\Cake\ORM\Association\HasMany $ScheduleViews
 * @property \App\Model\Table\SchedulesTable&\Cake\ORM\Association\HasMany $Schedules
 *
 * @method \App\Model\Entity\Campaign newEmptyEntity()
 * @method \App\Model\Entity\Campaign newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Campaign[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Campaign get($primaryKey, $options = [])
 * @method \App\Model\Entity\Campaign findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Campaign patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Campaign[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Campaign|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Campaign saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Campaign[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Campaign[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Campaign[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Campaign[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CampaignsTable extends Table
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

        $this->setTable('campaigns');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Templates', [
            'foreignKey' => 'template_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('CampaignForms', [
            'foreignKey' => 'campaign_id',
        ]);
        $this->hasMany('CampsTrackers', [
            'foreignKey' => 'campaign_id',
        ]);
        $this->hasMany('ScheduleViews', [
            'foreignKey' => 'campaign_id',
        ]);
        $this->hasMany('Schedules', [
            'foreignKey' => 'campaign_id',
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
            ->scalar('campaign_name')
            ->maxLength('campaign_name', 128)
            ->requirePresence('campaign_name', 'create')
            ->notEmptyString('campaign_name')
            ->add('campaign_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->date('start_date')
            ->requirePresence('start_date', 'create')
            ->notEmptyDate('start_date');

        $validator
            ->date('end_date')
            ->requirePresence('end_date', 'create')
            ->notEmptyDate('end_date');

        $validator
            ->boolean('auto_inject')
            ->notEmptyString('auto_inject');

        $validator
            ->scalar('inject_text')
            ->allowEmptyString('inject_text');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->notEmptyString('template_id');

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
        $rules->add($rules->isUnique(['campaign_name']), ['errorField' => 'campaign_name']);
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn('template_id', 'Templates'), ['errorField' => 'template_id']);

        return $rules;
    }
}
