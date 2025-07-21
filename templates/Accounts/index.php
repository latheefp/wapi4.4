<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Account> $accounts
 */
?>
<div class="accounts index content">
    <?= $this->Html->link(__('New Account'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Accounts') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('company_name') ?></th>
                    <th><?= $this->Paginator->sort('primary_contact_person') ?></th>
                    <th><?= $this->Paginator->sort('primary_number') ?></th>
                    <th><?= $this->Paginator->sort('secondary_number') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('current_balance') ?></th>
                    <th><?= $this->Paginator->sort('WBAID') ?></th>
                    <th><?= $this->Paginator->sort('API_VERSION') ?></th>
                    <th><?= $this->Paginator->sort('ACCESSTOKENVALUE') ?></th>
                    <th><?= $this->Paginator->sort('phone_number_id') ?></th>
                    <th><?= $this->Paginator->sort('def_language') ?></th>
                    <th><?= $this->Paginator->sort('test_number') ?></th>
                    <th><?= $this->Paginator->sort('restricted_start_time') ?></th>
                    <th><?= $this->Paginator->sort('restricted_end_time') ?></th>
                    <th><?= $this->Paginator->sort('interactive_webhook') ?></th>
                    <th><?= $this->Paginator->sort('rcv_notification_template') ?></th>
                    <th><?= $this->Paginator->sort('interactive_api_key') ?></th>
                    <th><?= $this->Paginator->sort('interactive_menu_function') ?></th>
                    <th><?= $this->Paginator->sort('interactive_notification_numbers') ?></th>
                    <th><?= $this->Paginator->sort('def_isd') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?= $this->Number->format($account->id) ?></td>
                    <td><?= h($account->company_name) ?></td>
                    <td><?= h($account->primary_contact_person) ?></td>
                    <td><?= h($account->primary_number) ?></td>
                    <td><?= h($account->secondary_number) ?></td>
                    <td><?= h($account->created) ?></td>
                    <td><?= h($account->modified) ?></td>
                    <td><?= $this->Number->format($account->user_id) ?></td>
                    <td><?= $this->Number->format($account->current_balance) ?></td>
                    <td><?= h($account->WBAID) ?></td>
                    <td><?= h($account->API_VERSION) ?></td>
                    <td><?= h($account->ACCESSTOKENVALUE) ?></td>
                    <td><?= h($account->phone_number_id) ?></td>
                    <td><?= h($account->def_language) ?></td>
                    <td><?= h($account->test_number) ?></td>
                    <td><?= h($account->restricted_start_time) ?></td>
                    <td><?= h($account->restricted_end_time) ?></td>
                    <td><?= h($account->interactive_webhook) ?></td>
                    <td><?= h($account->rcv_notification_template) ?></td>
                    <td><?= h($account->interactive_api_key) ?></td>
                    <td><?= h($account->interactive_menu_function) ?></td>
                    <td><?= h($account->interactive_notification_numbers) ?></td>
                    <td><?= h($account->def_isd) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $account->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $account->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $account->id], ['confirm' => __('Are you sure you want to delete # {0}?', $account->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
