<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AccountsCountry $accountsCountry
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Accounts Country'), ['action' => 'edit', $accountsCountry->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Accounts Country'), ['action' => 'delete', $accountsCountry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accountsCountry->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Accounts Countries'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Accounts Country'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="accountsCountries view content">
            <h3><?= h($accountsCountry->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Account') ?></th>
                    <td><?= $accountsCountry->has('account') ? $this->Html->link($accountsCountry->account->name, ['controller' => 'Accounts', 'action' => 'view', $accountsCountry->account->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Country') ?></th>
                    <td><?= $accountsCountry->has('country') ? $this->Html->link($accountsCountry->country->name, ['controller' => 'Countries', 'action' => 'view', $accountsCountry->country->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($accountsCountry->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($accountsCountry->created) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
