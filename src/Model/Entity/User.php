<?php
declare(strict_types=1);

namespace App\Model\Entity;
use Cake\Auth\DefaultPasswordHasher;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $last_logged
 * @property bool $show_closed
 * @property string $ugroup_id
 * @property int $account_id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string|null $mobile_number
 * @property bool|null $active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $show_cols
 * @property int|null $login_count
 *
 * @property \App\Model\Entity\Ugroup $ugroup
 * @property \App\Model\Entity\Account[] $accounts
 * @property \App\Model\Entity\ApiKey[] $api_keys
 * @property \App\Model\Entity\Apiview[] $apiviews
 * @property \App\Model\Entity\CampaignView[] $campaign_views
 * @property \App\Model\Entity\Campaign[] $campaigns
 * @property \App\Model\Entity\Campaigns-blocked[] $campaigns_blocked
 * @property \App\Model\Entity\ContactNumbersView[] $contact_numbers_views
 * @property \App\Model\Entity\Contact[] $contacts
 * @property \App\Model\Entity\GroupsUser[] $groups_users
 * @property \App\Model\Entity\PointNotification[] $point_notifications
 * @property \App\Model\Entity\ScheduleView[] $schedule_views
 * @property \App\Model\Entity\Schedule[] $schedules
 * @property \App\Model\Entity\Upload[] $uploads
 */
class User extends Entity
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
        'last_logged' => true,
        'show_closed' => true,
        'ugroup_id' => true,
        'account_id' => true,
        'name' => true,
        'username' => true,
        'password' => true,
        'email' => true,
        'mobile_number' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'show_cols' => true,
        'login_count' => true,
        'ugroup' => true,
        'accounts' => true,
        'api_keys' => true,
        'apiviews' => true,
        'campaign_views' => true,
        'campaigns' => true,
        'campaigns_blocked' => true,
        'contact_numbers_views' => true,
        'contacts' => true,
        'groups_users' => true,
        'point_notifications' => true,
        'schedule_views' => true,
        'schedules' => true,
        'uploads' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected $_hidden = [
        'password',
    ];

      protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
    
}
