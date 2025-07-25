<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\AccountsCountry> $accountsCountries
 */
?>
<div class="accountsCountries index content">
    <?= $this->Html->link(__('New Accounts Country'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Accounts Countries') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('account_id') ?></th>
                    <th><?= $this->Paginator->sort('country_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accountsCountries as $accountsCountry): ?>
                <tr>
                    <td><?= $this->Number->format($accountsCountry->id) ?></td>
                    <td><?= $accountsCountry->has('account') ? $this->Html->link($accountsCountry->account->name, ['controller' => 'Accounts', 'action' => 'view', $accountsCountry->account->id]) : '' ?></td>
                    <td><?= $accountsCountry->has('country') ? $this->Html->link($accountsCountry->country->name, ['controller' => 'Countries', 'action' => 'view', $accountsCountry->country->id]) : '' ?></td>
                    <td><?= h($accountsCountry->created) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $accountsCountry->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $accountsCountry->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $accountsCountry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accountsCountry->id)]) ?>
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
