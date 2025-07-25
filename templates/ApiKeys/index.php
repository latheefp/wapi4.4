<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ApiKey> $apiKeys
 */
?>
<div class="apiKeys index content">
    <?= $this->Html->link(__('New Api Key'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Api Keys') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('api_name') ?></th>
                    <th><?= $this->Paginator->sort('api_key') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('enabled') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('ip_list') ?></th>
                    <th><?= $this->Paginator->sort('account_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apiKeys as $apiKey): ?>
                <tr>
                    <td><?= $this->Number->format($apiKey->id) ?></td>
                    <td><?= h($apiKey->api_name) ?></td>
                    <td><?= h($apiKey->api_key) ?></td>
                    <td><?= $apiKey->has('user') ? $this->Html->link($apiKey->user->name, ['controller' => 'Users', 'action' => 'view', $apiKey->user->id]) : '' ?></td>
                    <td><?= h($apiKey->enabled) ?></td>
                    <td><?= h($apiKey->created) ?></td>
                    <td><?= h($apiKey->ip_list) ?></td>
                    <td><?= $apiKey->has('account') ? $this->Html->link($apiKey->account->name, ['controller' => 'Accounts', 'action' => 'view', $apiKey->account->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $apiKey->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $apiKey->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $apiKey->id], ['confirm' => __('Are you sure you want to delete # {0}?', $apiKey->id)]) ?>
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
