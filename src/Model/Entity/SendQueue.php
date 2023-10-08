<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SendQueue Entity
 *
 * @property int $id
 * @property string $form_data
 * @property \Cake\I18n\FrozenTime $created
 * @property string $status
 * @property bool $processed
 */
class SendQueue extends Entity
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
        'form_data' => true,
        'created' => true,
        'status' => true,
        'processed' => true,
    ];
}
