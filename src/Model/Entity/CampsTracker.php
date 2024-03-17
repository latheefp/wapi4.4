<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CampsTracker Entity
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $contact_number_id
 * @property \Cake\I18n\FrozenTime $created
 * @property bool $lead
 * @property \Cake\I18n\FrozenTime|null $leadtime
 * @property \Cake\I18n\FrozenTime $modified
 * @property string|null $hashvalue
 * @property int $duplicate_blocked
 *
 * @property \App\Model\Entity\Campaign $campaign
 * @property \App\Model\Entity\ContactNumber $contact_number
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
        'campaign_id' => true,
        'contact_number_id' => true,
        'created' => true,
        'lead' => true,
        'leadtime' => true,
        'modified' => true,
        'hashvalue' => true,
        'duplicate_blocked' => true,
        'campaign' => true,
        'contact_number' => true,
    ];
}
