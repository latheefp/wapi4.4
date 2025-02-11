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
 * @property int $account_id
 * @property bool $camp_blocked
 * @property string|null $name
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\Account $account
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Chat[] $chats
 * @property \App\Model\Entity\RatingView[] $rating_views
 * @property \App\Model\Entity\RecentChat[] $recent_chats
 * @property \App\Model\Entity\StreamView[] $stream_views
 * @property \App\Model\Entity\Stream[] $streams
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
     * @var array<string, bool>
     */
    protected $_accessible = [
        'contact_number' => true,
        'profile_name' => true,
        'account_id' => true,
        'camp_blocked' => true,
        'name' => true,
        'user_id' => true,
        'created' => true,
        'account' => true,
        'user' => true,
        'chats' => true,
        'rating_views' => true,
        'recent_chats' => true,
        'stream_views' => true,
        'streams' => true,
    ];
}
