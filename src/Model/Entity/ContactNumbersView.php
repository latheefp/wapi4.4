<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContactNumbersView Entity
 *
 * @property int $id
 * @property string $mobile_number
 * @property string|null $name
 * @property string|null $gender
 * @property \Cake\I18n\FrozenDate|null $expiry
 * @property bool $whatsapp
 * @property bool $blocked
 * @property string|null $contact_name
 * @property int|null $user_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $contact_id
 * @property int|null $account_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Account $account
 */
class ContactNumbersView extends Entity
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
        'id' => true,
        'mobile_number' => true,
        'name' => true,
        'gender' => true,
        'expiry' => true,
        'whatsapp' => true,
        'blocked' => true,
        'contact_name' => true,
        'user_id' => true,
        'created' => true,
        'contact_id' => true,
        'account_id' => true,
        'user' => true,
        'account' => true,
    ];
}
