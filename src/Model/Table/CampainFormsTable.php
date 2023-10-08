<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CampainForms Model
 *
 * @property \App\Model\Table\CampaignsTable&\Cake\ORM\Association\BelongsTo $Campaigns
 *
 * @method \App\Model\Entity\CampainForm newEmptyEntity()
 * @method \App\Model\Entity\CampainForm newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CampainForm[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CampainForm get($primaryKey, $options = [])
 * @method \App\Model\Entity\CampainForm findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CampainForm patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CampainForm[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CampainForm|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CampainForm saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CampainForm[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CampainForm[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CampainForm[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CampainForm[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CampainFormsTable extends Table
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

        $this->setTable('campain_forms');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Campaigns', [
            'foreignKey' => 'campaign_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('field_type')
            ->maxLength('field_type', 32)
            ->allowEmptyString('field_type');

        $validator
            ->scalar('field_name')
            ->maxLength('field_name', 32)
            ->allowEmptyString('field_name');

        $validator
            ->scalar('field_value')
            ->maxLength('field_value', 128)
            ->allowEmptyString('field_value');

        $validator
            ->scalar('language')
            ->maxLength('language', 16)
            ->allowEmptyString('language');

        $validator
            ->scalar('file_type')
            ->maxLength('file_type', 32)
            ->allowEmptyFile('file_type');

        $validator
            ->scalar('file_path')
            ->allowEmptyFile('file_path');

        $validator
            ->integer('file_size')
            ->allowEmptyFile('file_size');

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
        $rules->add($rules->existsIn(['campaign_id'], 'Campaigns'), ['errorField' => 'campaign_id']);

        return $rules;
    }
}
