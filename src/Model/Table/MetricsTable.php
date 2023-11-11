<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Metrics Model
 *
 * @method \App\Model\Entity\Metric newEmptyEntity()
 * @method \App\Model\Entity\Metric newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Metric[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Metric get($primaryKey, $options = [])
 * @method \App\Model\Entity\Metric findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Metric patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Metric[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Metric|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Metric saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Metric[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Metric[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Metric[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Metric[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class MetricsTable extends Table
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

        $this->setTable('metrics');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->scalar('module_name')
            ->maxLength('module_name', 255)
            ->allowEmptyString('module_name');

        $validator
            ->integer('account')
            ->notEmptyString('account');

        $validator
            ->decimal('metric_value')
            ->allowEmptyString('metric_value');

        $validator
            ->dateTime('recorded_at')
            ->allowEmptyDateTime('recorded_at');

        return $validator;
    }
}
