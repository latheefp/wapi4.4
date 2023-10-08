<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Shedule Entity
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $user_id
 * @property \Cake\I18n\FrozenDate|null $created
 * @property string|null $status
 *
 * @property \App\Model\Entity\Campaign $campaign
 * @property \App\Model\Entity\User $user
 */
class Shedule extends Entity
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
        'campaign_id' => true,
        'user_id' => true,
        'created' => true,
        'status' => true,
        'campaign' => true,
        'user' => true,
    ];
}
