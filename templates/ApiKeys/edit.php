<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApiKey $apiKey
 * @var string[]|\Cake\Collection\CollectionInterface $users
 * @var string[]|\Cake\Collection\CollectionInterface $accounts
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $apiKey->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $apiKey->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Api Keys'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="apiKeys form content">
            <?= $this->Form->create($apiKey) ?>
            <fieldset>
                <legend><?= __('Edit Api Key') ?></legend>
                <?php
                    echo $this->Form->control('api_name');
                    echo $this->Form->control('api_key');
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('enabled');
                    echo $this->Form->control('ip_list');
                    echo $this->Form->control('account_id', ['options' => $accounts]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
