<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UgroupsPermission Entity
 *
 * @property int $id
 * @property int $ugroup_id
 * @property int $permission_id
 * @property int $account_id
 *
 * @property \App\Model\Entity\Ugroup $ugroup
 * @property \App\Model\Entity\Permission $permission
 * @property \App\Model\Entity\Company $company
 */
class UgroupsPermission extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'ugroup_id' => true,
        'permission_id' => true,
        'account_id' => true,
        'ugroup' => true,
        'permission' => true,
        'company' => true,
    ];
}
