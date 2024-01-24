<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InvoiceView Entity
 *
 * @property int $id
 * @property string|null $year
 * @property string|null $month
 * @property int|null $account_id
 * @property string|null $invoice_number
 * @property \Cake\I18n\FrozenDate|null $invoice_date
 * @property \Cake\I18n\FrozenDate|null $due_date
 * @property string|null $total_amount
 * @property string|null $status
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $company_name
 *
 * @property \App\Model\Entity\Account $account
 */
class InvoiceView extends Entity
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
        'id' => true,
        'year' => true,
        'month' => true,
        'account_id' => true,
        'invoice_number' => true,
        'invoice_date' => true,
        'due_date' => true,
        'total_amount' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'company_name' => true,
        'account' => true,
    ];
}
