<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApiKey Entity
 *
 * @property int $id
 * @property string $api_name
 * @property string|null $api_key
 * @property int|null $user_id
 * @property bool $enabled
 * @property \Cake\I18n\FrozenDate $created
 * @property string|null $ip_list
 * @property int $account_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Account $account
 */
class ApiKey extends Entity
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
        'api_name' => true,
        'api_key' => true,
        'user_id' => true,
        'enabled' => true,
        'created' => true,
        'ip_list' => true,
        'account_id' => true,
        'user' => true,
        'account' => true,
    ];
}
