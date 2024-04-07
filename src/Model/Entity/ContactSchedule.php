<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContactSchedule Entity
 *
 * @property int $id
 * @property int $schedule_id
 * @property string $contact_waid
 *
 * @property \App\Model\Entity\Schedule $schedule
 */
class ContactSchedule extends Entity
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
        'schedule_id' => true,
        'contact_waid' => true,
        'schedule' => true,
    ];
}
