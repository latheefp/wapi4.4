<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Ugroups Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 * @property \App\Model\Table\PermissionsTable&\Cake\ORM\Association\BelongsToMany $Permissions
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsToMany $Users
 *
 * @method \App\Model\Entity\Ugroup newEmptyEntity()
 * @method \App\Model\Entity\Ugroup newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Ugroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Ugroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\Ugroup findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Ugroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Ugroup[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Ugroup|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Ugroup saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Ugroup[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Ugroup[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Ugroup[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Ugroup[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class UgroupsTable extends Table
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

        $this->setTable('ugroups');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Users', [
            'foreignKey' => 'ugroup_id',
        ]);
        $this->belongsToMany('Permissions', [
            'foreignKey' => 'ugroup_id',
            'targetForeignKey' => 'permission_id',
            'joinTable' => 'ugroups_permissions',
        ]);
        $this->belongsToMany('Users', [
            'foreignKey' => 'ugroup_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'ugroups_users',
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
            ->scalar('groupname')
            ->maxLength('groupname', 32)
            ->requirePresence('groupname', 'create')
            ->notEmptyString('groupname');

        return $validator;
    }
}
