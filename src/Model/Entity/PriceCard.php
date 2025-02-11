<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PriceCard Entity
 *
 * @property int $id
 * @property string $country
 * @property string $country_code
 * @property float $marketing
 * @property float $utility
 * @property float $authentication
 * @property float $service
 * @property float $business_Initiated_rate
 * @property int|null $authentication_international
 * @property float $user_Initiated_rate
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $market
 * @property bool $updated
 */
class PriceCard extends Entity
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
        'country' => true,
        'country_code' => true,
        'marketing' => true,
        'utility' => true,
        'authentication' => true,
        'service' => true,
        'business_Initiated_rate' => true,
        'authentication_international' => true,
        'user_Initiated_rate' => true,
        'modified' => true,
        'market' => true,
        'updated' => true,
    ];
}
