<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApiKey $apiKey
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Api Key'), ['action' => 'edit', $apiKey->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Api Key'), ['action' => 'delete', $apiKey->id], ['confirm' => __('Are you sure you want to delete # {0}?', $apiKey->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Api Keys'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Api Key'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="apiKeys view content">
            <h3><?= h($apiKey->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Api Name') ?></th>
                    <td><?= h($apiKey->api_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Api Key') ?></th>
                    <td><?= h($apiKey->api_key) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $apiKey->has('user') ? $this->Html->link($apiKey->user->name, ['controller' => 'Users', 'action' => 'view', $apiKey->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Ip List') ?></th>
                    <td><?= h($apiKey->ip_list) ?></td>
                </tr>
                <tr>
                    <th><?= __('Account') ?></th>
                    <td><?= $apiKey->has('account') ? $this->Html->link($apiKey->account->name, ['controller' => 'Accounts', 'action' => 'view', $apiKey->account->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($apiKey->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($apiKey->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Enabled') ?></th>
                    <td><?= $apiKey->enabled ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
