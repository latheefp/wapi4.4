<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Notification Entity
 *
 * @property int $id
 * @property string $severity
 * @property string|null $details
 * @property \Cake\I18n\FrozenTime $created
 * @property string|null $line
 * @property string|null $function
 */
class Notification extends Entity
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
        'severity' => true,
        'details' => true,
        'created' => true,
        'line' => true,
        'function' => true,
    ];
}
