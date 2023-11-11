<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Metric Entity
 *
 * @property int $id
 * @property string|null $module_name
 * @property int $account
 * @property string|null $metric_value
 * @property \Cake\I18n\FrozenTime|null $recorded_at
 */
class Metric extends Entity
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
        'module_name' => true,
        'account' => true,
        'metric_value' => true,
        'recorded_at' => true,
    ];
}
