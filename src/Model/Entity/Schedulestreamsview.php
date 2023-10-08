<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Schedulestreamsview Entity
 *
 * @property int $id
 * @property string|null $hookid
 * @property string|null $messaging_product
 * @property string|null $display_phone_number
 * @property string|null $phonenumberid
 * @property string|null $contacts_profile_name
 * @property string|null $contact_waid
 * @property int|null $schedule_id
 * @property string $lang
 * @property string|null $contact_id
 * @property string|null $message_context_from
 * @property string|null $message_from
 * @property \Cake\I18n\FrozenTime|null $message_timestamp
 * @property string|null $message_txt_body
 * @property string|null $message_context
 * @property string|null $message_contextId
 * @property string|null $message_contextFrom
 * @property string|null $messageid
 * @property string|null $replyid
 * @property \Cake\I18n\FrozenTime|null $timestamp
 * @property string|null $type
 * @property bool $has_wa
 * @property string|null $message_format_type
 * @property \Cake\I18n\FrozenTime|null $read_time
 * @property \Cake\I18n\FrozenTime|null $delivered_time
 * @property \Cake\I18n\FrozenTime|null $sent_time
 * @property string|null $button_payload
 * @property string|null $button_text
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Schedule $schedule
 * @property \App\Model\Entity\Contact $contact
 */
class Schedulestreamsview extends Entity
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
        'hookid' => true,
        'messaging_product' => true,
        'display_phone_number' => true,
        'phonenumberid' => true,
        'contacts_profile_name' => true,
        'contact_waid' => true,
        'schedule_id' => true,
        'lang' => true,
        'contact_id' => true,
        'message_context_from' => true,
        'message_from' => true,
        'message_timestamp' => true,
        'message_txt_body' => true,
        'message_context' => true,
        'message_contextId' => true,
        'message_contextFrom' => true,
        'messageid' => true,
        'replyid' => true,
        'timestamp' => true,
        'type' => true,
        'has_wa' => true,
        'message_format_type' => true,
        'read_time' => true,
        'delivered_time' => true,
        'sent_time' => true,
        'button_payload' => true,
        'button_text' => true,
        'created' => true,
        'modified' => true,
        'schedule' => true,
        'contact' => true,
    ];
}
