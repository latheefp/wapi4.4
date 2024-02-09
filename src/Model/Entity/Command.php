<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Command Entity
 *
 * @property int $id
 * @property int $account_id
 * @property string|null $cmd
 * @property string|null $function
 * @property string|null $help_text
 *
 * @property \App\Model\Entity\Account $account
 */
class Command extends Entity
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
        'cmd' => true,
        'function' => true,
        'help_text' => true,
        'account' => true,
    ];
}
