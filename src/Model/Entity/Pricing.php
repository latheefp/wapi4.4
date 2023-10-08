<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Pricing Entity
 *
 * @property int $id
 * @property int $stream_id
 * @property \Cake\I18n\FrozenTime $created
 * @property string $category
 * @property bool $billable
 * @property string $pricing_model
 *
 * @property \App\Model\Entity\Stream $stream
 */
class Pricing extends Entity
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
        'stream_id' => true,
        'created' => true,
        'category' => true,
        'billable' => true,
        'pricing_model' => true,
        'stream' => true,
    ];
}
