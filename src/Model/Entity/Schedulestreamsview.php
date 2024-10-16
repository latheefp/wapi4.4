<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Schedulestreamsview Entity
 *
 * @property string $lang
 * @property \Cake\I18n\FrozenTime|null $sent_time
 * @property \Cake\I18n\FrozenTime|null $delivered_time
 * @property \Cake\I18n\FrozenTime|null $read_time
 * @property bool $has_wa
 * @property int|null $schedule_id
 * @property string|null $contact_waid
 *
 * @property \App\Model\Entity\Schedule $schedule
 * @property \App\Model\Entity\Contact $contact
 */
class Schedulestreamsview extends Entity
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
        'lang' => true,
        'sent_time' => true,
        'delivered_time' => true,
        'read_time' => true,
        'has_wa' => true,
        'schedule_id' => true,
        'contact_waid' => true,
        'schedule' => true,
        'contact' => true,
    ];
}
