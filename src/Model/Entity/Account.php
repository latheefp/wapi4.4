<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Account Entity
 *
 * @property int $id
 * @property string $company_name
 * @property string $Address
 * @property string $primary_contact_person
 * @property string $primary_number
 * @property string|null $secondary_number
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $user_id
 * @property float $current_balance
 * @property string $WBAID
 * @property string $API_VERSION
 * @property string $ACCESSTOKENVALUE
 * @property string $phone_numberId
 * @property string $def_language
 * @property string $test_number
 * @property \Cake\I18n\Time $restricted_start_time
 * @property \Cake\I18n\Time $restricted_end_time
 * @property string $interactive_webhook
 * @property string|null $rcv_notification_template
 * @property string $interactive_api_key
 * @property string|null $interactive_menu_function
 * @property string $interactive_notification_numbers
 * @property string $def_isd
 * @property string|null $welcome_msg
 * @property string $webhook_token
 * @property bool $webhookverified
 *
 * @property \App\Model\Entity\User[] $users
 * @property \App\Model\Entity\ApiKey[] $api_keys
 * @property \App\Model\Entity\Apiview[] $apiviews
 * @property \App\Model\Entity\CampaignView[] $campaign_views
 * @property \App\Model\Entity\Chat[] $chats
 * @property \App\Model\Entity\ChatsSession[] $chats_sessions
 * @property \App\Model\Entity\Command[] $commands
 * @property \App\Model\Entity\ContactStream[] $contact_streams
 * @property \App\Model\Entity\Contact[] $contacts
 * @property \App\Model\Entity\InvoiceView[] $invoice_views
 * @property \App\Model\Entity\Invoice[] $invoices
 * @property \App\Model\Entity\Permission[] $permissions
 * @property \App\Model\Entity\RecentChat[] $recent_chats
 * @property \App\Model\Entity\ScheduleView[] $schedule_views
 * @property \App\Model\Entity\Schedule[] $schedules
 * @property \App\Model\Entity\StreamView[] $stream_views
 * @property \App\Model\Entity\Stream[] $streams
 * @property \App\Model\Entity\Template[] $templates
 * @property \App\Model\Entity\UgroupsPermission[] $ugroups_permissions
 * @property \App\Model\Entity\Country[] $countries
 */
class Account extends Entity
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
        'company_name' => true,
        'Address' => true,
        'primary_contact_person' => true,
        'primary_number' => true,
        'secondary_number' => true,
        'created' => true,
        'modified' => true,
        'user_id' => true,
        'current_balance' => true,
        'WBAID' => true,
        'API_VERSION' => true,
        'ACCESSTOKENVALUE' => true,
        'phone_numberId' => true,
        'def_language' => true,
        'test_number' => true,
        'restricted_start_time' => true,
        'restricted_end_time' => true,
        'interactive_webhook' => true,
        'rcv_notification_template' => true,
        'interactive_api_key' => true,
        'interactive_menu_function' => true,
        'interactive_notification_numbers' => true,
        'def_isd' => true,
        'welcome_msg' => true,
        'webhook_token' => true,
        'webhookverified' => true,
        'users' => true,
        'api_keys' => true,
        'apiviews' => true,
        'campaign_views' => true,
        'chats' => true,
        'chats_sessions' => true,
        'commands' => true,
        'contact_streams' => true,
        'contacts' => true,
        'invoice_views' => true,
        'invoices' => true,
        'permissions' => true,
        'recent_chats' => true,
        'schedule_views' => true,
        'schedules' => true,
        'stream_views' => true,
        'streams' => true,
        'templates' => true,
        'ugroups_permissions' => true,
        'countries' => true,
    ];
}
