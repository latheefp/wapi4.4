<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContactsSchedule Entity
 *
 * @property int $id
 * @property int $schedule_id
 * @property string $contact_number
 * @property string $lang
 * @property string|null $wamsgId
 * @property \Cake\I18n\FrozenTime|null $sent_time
 * @property \Cake\I18n\FrozenTime|null $read_time
 * @property \Cake\I18n\FrozenTime|null $deliverd_time
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Schedule $schedule
 */
class ContactsSchedule extends Entity
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
        'schedule_id' => true,
        'contact_number' => true,
        'lang' => true,
        'wamsgId' => true,
        'sent_time' => true,
        'read_time' => true,
        'deliverd_time' => true,
        'created' => true,
        'modified' => true,
        'schedule' => true,
    ];
}
