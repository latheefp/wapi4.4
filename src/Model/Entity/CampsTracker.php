<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CampsTracker Entity
 *
 * @property int $id
 * @property int $campain_id
 * @property int $contact_stream_id
 * @property \Cake\I18n\FrozenTime $created
 * @property bool $lead
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\ContactStream $contact_stream
 */
class CampsTracker extends Entity
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
        'campain_id' => true,
        'contact_stream_id' => true,
        'created' => true,
        'lead' => true,
        'modified' => true,
        'contact_stream' => true,
    ];
}
