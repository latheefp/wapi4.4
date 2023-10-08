<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContactStream Entity
 *
 * @property int $id
 * @property string $contact_number
 * @property string|null $profile_name
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\StreamView[] $stream_views
 * @property \App\Model\Entity\Stream[] $streams
 * @property \App\Model\Entity\Streams-25-jan[] $streams_25_jan
 */
class ContactStream extends Entity
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
        'contact_number' => true,
        'profile_name' => true,
        'name' => true,
        'created' => true,
        'stream_views' => true,
        'streams' => true,
        'streams_25_jan' => true,
    ];
}
