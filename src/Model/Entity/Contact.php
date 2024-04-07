<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Contact Entity
 *
 * @property int $id
 * @property string $name
 * @property int $contact_count
 * @property int $whatsapp_count
 * @property int $blocked_count
 * @property \Cake\I18n\FrozenTime $created
 * @property int $account_id
 * @property int $user_id
 *
 * @property \App\Model\Entity\Account $account
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\ContactNumber[] $contact_numbers
 */
class Contact extends Entity
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
        'name' => true,
        'contact_count' => true,
        'whatsapp_count' => true,
        'blocked_count' => true,
        'created' => true,
        'account_id' => true,
        'user_id' => true,
        'account' => true,
        'user' => true,
        'contact_numbers' => true,
    ];
}
