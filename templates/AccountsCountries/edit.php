<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AccountsCountry $accountsCountry
 * @var string[]|\Cake\Collection\CollectionInterface $accounts
 * @var string[]|\Cake\Collection\CollectionInterface $countries
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $accountsCountry->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $accountsCountry->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Accounts Countries'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="accountsCountries form content">
            <?= $this->Form->create($accountsCountry) ?>
            <fieldset>
                <legend><?= __('Edit Accounts Country') ?></legend>
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
