<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Apiview Entity
 *
 * @property int $id
 * @property string $api_name
 * @property string|null $api_key
 * @property int $user_id
 * @property bool $enabled
 * @property string $ip_list
 * @property string|null $username
 *
 * @property \App\Model\Entity\User $user
 */
class Apiview extends Entity
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
        'api_name' => true,
        'api_key' => true,
        'user_id' => true,
        'enabled' => true,
        'ip_list' => true,
        'username' => true,
        'user' => true,
    ];
}
