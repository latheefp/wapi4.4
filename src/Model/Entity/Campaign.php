<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Campaign Entity
 *
 * @property int $id
 * @property string $campaign_name
 * @property \Cake\I18n\FrozenDate $start_date
 * @property \Cake\I18n\FrozenDate $end_date
 * @property bool $auto_inject
 * @property string|null $inject_text
 * @property \Cake\I18n\FrozenTime $created
 * @property int $user_id
 * @property int $template_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Template $template
 * @property \App\Model\Entity\CampaignForm[] $campaign_forms
 * @property \App\Model\Entity\CampsTracker[] $camps_trackers
 * @property \App\Model\Entity\ScheduleView[] $schedule_views
 * @property \App\Model\Entity\Schedule[] $schedules
 */
class Campaign extends Entity
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
        'campaign_name' => true,
        'start_date' => true,
        'end_date' => true,
        'auto_inject' => true,
        'inject_text' => true,
        'created' => true,
        'user_id' => true,
        'template_id' => true,
        'user' => true,
        'template' => true,
        'campaign_forms' => true,
        'camps_trackers' => true,
        'schedule_views' => true,
        'schedules' => true,
    ];
}
