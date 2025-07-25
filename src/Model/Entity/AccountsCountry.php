<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AccountsCountry Entity
 *
 * @property int $id
 * @property int $account_id
 * @property int $country_id
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\Account $account
 * @property \App\Model\Entity\Country $country
 */
class AccountsCountry extends Entity
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
        'account_id' => true,
        'country_id' => true,
        'created' => true,
        'account' => true,
        'country' => true,
    ];
}
