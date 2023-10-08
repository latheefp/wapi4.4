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
 * @property string $phone_number_id
 * @property string $def_language
 * @property string $test_number
 * @property \Cake\I18n\Time $restricted_start_time
 * @property \Cake\I18n\Time $restricted_end_time
 * @property string $interactive_webhook
 * @property string $interactive_api_key
 * @property string|null $interactive_menu_function
 * @property string $interactive_notification_numbers
 * @property string $def_isd
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Group[] $groups
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
     * @var array
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
        'phone_number_id' => true,
        'def_language' => true,
        'test_number' => true,
        'restricted_start_time' => true,
        'restricted_end_time' => true,
        'interactive_webhook' => true,
        'interactive_api_key' => true,
        'interactive_menu_function' => true,
        'interactive_notification_numbers' => true,
        'def_isd' => true,
        'user' => true,
        'groups' => true,
    ];
}
