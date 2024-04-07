<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContactNumber Entity
 *
 * @property int $id
 * @property string $mobile_number
 * @property string|null $name
 * @property string|null $gender
 * @property \Cake\I18n\FrozenDate|null $expiry
 * @property bool $whatsapp
 * @property bool $blocked
 *
 * @property \App\Model\Entity\CampsTracker[] $camps_trackers
 * @property \App\Model\Entity\Contact[] $contacts
 */
class ContactNumber extends Entity
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
        'name' => true,
        'gender' => true,
        'expiry' => true,
        'whatsapp' => true,
        'blocked' => true,
        'camps_trackers' => true,
        'contacts' => true,
    ];
}
