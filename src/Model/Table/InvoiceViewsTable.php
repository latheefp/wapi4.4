<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * InvoiceViews Model
 *
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 *
 * @method \App\Model\Entity\InvoiceView newEmptyEntity()
 * @method \App\Model\Entity\InvoiceView newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\InvoiceView[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\InvoiceView get($primaryKey, $options = [])
 * @method \App\Model\Entity\InvoiceView findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\InvoiceView patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\InvoiceView[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\InvoiceView|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\InvoiceView saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\InvoiceView[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\InvoiceView[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\InvoiceView[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\InvoiceView[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class InvoiceViewsTable extends Table
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

        $this->setTable('invoice_views');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
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
            ->scalar('year')
            ->maxLength('year', 4)
            ->allowEmptyString('year');

        $validator
            ->scalar('month')
            ->maxLength('month', 2)
            ->allowEmptyString('month');

        $validator
            ->integer('account_id')
            ->allowEmptyString('account_id');

        $validator
            ->scalar('invoice_number')
            ->maxLength('invoice_number', 20)
            ->allowEmptyString('invoice_number');

        $validator
            ->date('invoice_date')
            ->allowEmptyDate('invoice_date');

        $validator
            ->date('due_date')
            ->allowEmptyDate('due_date');

        $validator
            ->decimal('total_amount')
            ->allowEmptyString('total_amount');

        $validator
            ->scalar('status')
            ->allowEmptyString('status');

        $validator
            ->scalar('company_name')
            ->maxLength('company_name', 32)
            ->allowEmptyString('company_name');

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

        return $rules;
    }
}
