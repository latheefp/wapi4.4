<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ScheduleView Entity
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $user_id
 * @property \Cake\I18n\FrozenDate|null $created
 * @property string|null $status
 * @property string|null $name
 * @property string|null $template_status
 * @property string|null $campaign_name
 * @property \Cake\I18n\FrozenDate|null $start_date
 * @property \Cake\I18n\FrozenDate|null $end_date
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Campaign $campaign
 */
class ScheduleView extends Entity
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
        'campaign_id' => true,
        'user_id' => true,
        'created' => true,
        'status' => true,
        'name' => true,
        'template_status' => true,
        'campaign_name' => true,
        'start_date' => true,
        'end_date' => true,
        'user' => true,
        'campaign' => true,
    ];
}
