<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Contactform Entity
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $company
 * @property string $contact_number
 * @property int $msg_per_month
 * @property int $msg_per_day
 * @property string $target_country
 * @property string|null $details
 * @property \Cake\I18n\FrozenTime $created
 */
class Contactform extends Entity
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
        'first_name' => true,
        'last_name' => true,
        'email' => true,
        'company' => true,
        'contact_number' => true,
        'msg_per_month' => true,
        'msg_per_day' => true,
        'target_country' => true,
        'details' => true,
        'created' => true,
    ];
}
