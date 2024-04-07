<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Stream Entity
 *
 * @property int $id
 * @property string|null $hookid
 * @property string|null $messaging_product
 * @property string|null $display_phone_number
 * @property string|null $phonenumberid
 * @property int|null $contact_stream_id
 * @property int|null $schedule_id
 * @property string $lang
 * @property string|null $message_context_from
 * @property string|null $message_from
 * @property \Cake\I18n\FrozenTime|null $message_timestamp
 * @property string|null $message_txt_body
 * @property string|null $message_context
 * @property string|null $message_contextId
 * @property string|null $message_contextFrom
 * @property string|null $messageid
 * @property string $initiator
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
 * @property string|null $sendarray
 * @property string|null $postdata
 * @property string|null $recievearray
 * @property string|null $result
 * @property bool|null $billable
 * @property string|null $pricing_model
 * @property float|null $costed
 * @property bool $rated
 * @property string|null $category
 * @property bool|null $success
 * @property string|null $errors
 * @property bool $commented
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $conversationid
 * @property int $account_id
 * @property \Cake\I18n\FrozenTime|null $conversation_expiration_timestamp
 * @property string|null $conversation_origin_type
 * @property string|null $tmp_upate_json
 *
 * @property \App\Model\Entity\ContactStream $contact_stream
 * @property \App\Model\Entity\Schedule $schedule
 * @property \App\Model\Entity\Contact $contact
 * @property \App\Model\Entity\Account $account
 * @property \App\Model\Entity\RatingView[] $rating_views
 * @property \App\Model\Entity\Rating[] $ratings
 * @property \App\Model\Entity\Ratings-aug-30[] $ratings_aug_30
 * @property \App\Model\Entity\StreamsUpdate[] $streams_updates
 */
class Stream extends Entity
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
        'hookid' => true,
        'messaging_product' => true,
        'display_phone_number' => true,
        'phonenumberid' => true,
        'contact_stream_id' => true,
        'schedule_id' => true,
        'lang' => true,
        'message_context_from' => true,
        'message_from' => true,
        'message_timestamp' => true,
        'message_txt_body' => true,
        'message_context' => true,
        'message_contextId' => true,
        'message_contextFrom' => true,
        'messageid' => true,
        'initiator' => true,
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
        'sendarray' => true,
        'postdata' => true,
        'recievearray' => true,
        'result' => true,
        'billable' => true,
        'pricing_model' => true,
        'costed' => true,
        'rated' => true,
        'category' => true,
        'success' => true,
        'errors' => true,
        'commented' => true,
        'created' => true,
        'modified' => true,
        'conversationid' => true,
        'account_id' => true,
        'conversation_expiration_timestamp' => true,
        'conversation_origin_type' => true,
        'tmp_upate_json' => true,
        'contact_stream' => true,
        'schedule' => true,
        'contact' => true,
        'account' => true,
        'rating_views' => true,
        'ratings' => true,
        'ratings_aug_30' => true,
        'streams_updates' => true,
    ];
}
