<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AccountsCountry $accountsCountry
 * @var \Cake\Collection\CollectionInterface|string[] $accounts
 * @var \Cake\Collection\CollectionInterface|string[] $countries
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Accounts Countries'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="accountsCountries form content">
            <?= $this->Form->create($accountsCountry) ?>
            <fieldset>
                <legend><?= __('Add Accounts Country') ?></legend>
                <?php
                    echo $this->Form->control('account_id', ['options' => $accounts]);
                    echo $this->Form->control('country_id', ['options' => $countries]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
