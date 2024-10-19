<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BlockedNumber Entity
 *
 * @property int $id
 * @property string $mobile_number
 * @property int $account_id
 * @property \Cake\I18n\FrozenTime $created
 * @property int $user_id
 *
 * @property \App\Model\Entity\Account $account
 * @property \App\Model\Entity\User $user
 */
class BlockedNumber extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'mobile_number' => true,
        'account_id' => true,
        'created' => true,
        'user_id' => true,
        'account' => true,
        'user' => true,
    ];
}
