<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Chat Entity
 *
 * @property int $id
 * @property string|null $sendarray
 * @property string|null $recievearray
 * @property \Cake\I18n\FrozenTime $created
 * @property int|null $contact_stream_id
 * @property int|null $account_id
 * @property int $stream_id
 * @property string|null $type
 *
 * @property \App\Model\Entity\ContactStream $contact_stream
 * @property \App\Model\Entity\Account $account
 * @property \App\Model\Entity\Stream $stream
 * @property \App\Model\Entity\Session[] $sessions
 */
class Chat extends Entity
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
        'sendarray' => true,
        'recievearray' => true,
        'created' => true,
        'contact_stream_id' => true,
        'account_id' => true,
        'stream_id' => true,
        'type' => true,
        'contact_stream' => true,
        'account' => true,
        'stream' => true,
        'sessions' => true,
    ];
}
