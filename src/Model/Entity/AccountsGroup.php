<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AccountsGroup Entity
 *
 * @property int $id
 * @property int $account_id
 * @property int $group_id
 *
 * @property \App\Model\Entity\Account $account
 * @property \App\Model\Entity\Group $group
 */
class AccountsGroup extends Entity
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
        'account_id' => true,
        'group_id' => true,
        'account' => true,
        'group' => true,
    ];
}
