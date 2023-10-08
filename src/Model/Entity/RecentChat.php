<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RecentChat Entity
 *
 * @property int|null $id
 * @property int|null $contact_stream_id
 * @property string|null $contact_number
 * @property string|null $name
 * @property string|null $profile_name
 *
 * @property \App\Model\Entity\ContactStream $contact_stream
 */
class RecentChat extends Entity
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
        'contact_stream_id' => true,
        'contact_number' => true,
        'name' => true,
        'profile_name' => true,
        'contact_stream' => true,
    ];
}
