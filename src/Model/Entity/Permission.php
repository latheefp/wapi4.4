<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Permission Entity
 *
 * @property int $id
 * @property string $permstring
 * @property string $category
 * @property int $account_id
 * @property string $description
 *
 * @property \App\Model\Entity\Ugroup[] $ugroups
 */
class Permission extends Entity
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
        'permstring' => true,
        'category' => true,
        'account_id' => true,
        'description' => true,
        'ugroups' => true,
    ];
}
