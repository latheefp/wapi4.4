<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Schedule Entity
 *
 * @property int $id
 * @property string $name
 * @property int $campaign_id
 * @property int $user_id
 * @property int $account_id
 * @property \Cake\I18n\FrozenDate|null $created
 * @property string|null $status
 * @property string|null $contact_csv
 * @property string $http_response_code
 * @property int|null $progress
 * @property int $total_contact
 *
 * @property \App\Model\Entity\Campaign $campaign
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Account $account
 * @property \App\Model\Entity\Schedulestreamsview[] $schedulestreamsviews
 * @property \App\Model\Entity\StreamView[] $stream_views
 * @property \App\Model\Entity\Stream[] $streams
 * @property \App\Model\Entity\Contact[] $contacts
 */
class Schedule extends Entity
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
        'name' => true,
        'campaign_id' => true,
        'user_id' => true,
        'account_id' => true,
        'created' => true,
        'status' => true,
        'contact_csv' => true,
        'http_response_code' => true,
        'progress' => true,
        'total_contact' => true,
        'campaign' => true,
        'user' => true,
        'account' => true,
        'schedulestreamsviews' => true,
        'stream_views' => true,
        'streams' => true,
        'contacts' => true,
    ];
}
