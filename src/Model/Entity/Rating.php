<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Rating Entity
 *
 * @property int $id
 * @property int $stream_id
 * @property float $old_balance
 * @property float $new_balance
 * @property float $cost
 * @property string $country
 * @property bool $charging_status
 * @property float $tax
 * @property float $p_perc
 * @property float|null $fb_cost
 * @property string|null $conversation
 * @property float $rate_with_tax
 * @property int|null $invoice_id
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\Stream $stream
 */
class Rating extends Entity
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
        'stream_id' => true,
        'old_balance' => true,
        'new_balance' => true,
        'cost' => true,
        'country' => true,
        'charging_status' => true,
        'tax' => true,
        'p_perc' => true,
        'fb_cost' => true,
        'conversation' => true,
        'rate_with_tax' => true,
        'invoice_id' => true,
        'created' => true,
        'stream' => true,
    ];
}
