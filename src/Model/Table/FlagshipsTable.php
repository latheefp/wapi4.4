<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Flagships Model
 *
 * @method \App\Model\Entity\Flagship newEmptyEntity()
 * @method \App\Model\Entity\Flagship newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Flagship[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Flagship get($primaryKey, $options = [])
 * @method \App\Model\Entity\Flagship findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Flagship patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Flagship[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Flagship|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Flagship saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Flagship[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Flagship[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Flagship[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Flagship[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class FlagshipsTable extends Table
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

        $this->setTable('flagships');
        $this->setDisplayField('title');
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('tbl_name')
            ->maxLength('tbl_name', 32)
            ->requirePresence('tbl_name', 'create')
            ->notEmptyString('tbl_name');

        $validator
            ->scalar('fld_name')
            ->maxLength('fld_name', 32)
            ->requirePresence('fld_name', 'create')
            ->notEmptyString('fld_name');

        $validator
            ->integer('order_index')
            ->notEmptyString('order_index');

        $validator
            ->scalar('title')
            ->maxLength('title', 32)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->boolean('searchable')
            ->notEmptyString('searchable');

        $validator
            ->boolean('exportable')
            ->notEmptyString('exportable');

        $validator
            ->boolean('viewable')
            ->notEmptyString('viewable');

        $validator
            ->scalar('format')
            ->maxLength('format', 64)
            ->allowEmptyString('format');

        $validator
            ->scalar('lists')
            ->maxLength('lists', 512)
            ->allowEmptyString('lists');

        $validator
            ->integer('width')
            ->notEmptyString('width');

        return $validator;
    }
}
