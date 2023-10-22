<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RcvQueue Entity
 *
 * @property int $id
 * @property string|null $status
 * @property string|null $json
 * @property \Cake\I18n\FrozenTime|null $created
 * @property bool|null $processed
 * @property \Cake\I18n\FrozenTime|null $process_start_time
 * @property \Cake\I18n\FrozenTime|null $process_end_time
 * @property string $http_response_code
 */
class RcvQueue extends Entity
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
        'status' => true,
        'json' => true,
        'created' => true,
        'processed' => true,
        'process_start_time' => true,
        'process_end_time' => true,
        'http_response_code' => true,
    ];
}
