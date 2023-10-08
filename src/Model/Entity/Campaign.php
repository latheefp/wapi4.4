<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Campaign Entity
 *
 * @property int $id
 * @property string $campaign_name
 * @property \Cake\I18n\FrozenTime $start_date
 * @property \Cake\I18n\FrozenTime $end_date
 * @property \Cake\I18n\FrozenTime $created
 * @property int $user_id
 * @property int $template_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Template $template
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
     * @var array
     */
    protected $_accessible = [
        'campaign_name' => true,
        'start_date' => true,
        'end_date' => true,
        'created' => true,
        'user_id' => true,
        'template_id' => true,
        'user' => true,
        'template' => true,
    ];
}
