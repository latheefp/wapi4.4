<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $account->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $account->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Accounts'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="accounts form content">
            <?= $this->Form->create($account) ?>
            <fieldset>
                <legend><?= __('Edit Account') ?></legend>
                <?php
                    echo $this->Form->control('company_name');
                    echo $this->Form->control('Address');
                    echo $this->Form->control('primary_contact_person');
                    echo $this->Form->control('primary_number');
                    echo $this->Form->control('secondary_number');
                    echo $this->Form->control('user_id');
                    echo $this->Form->control('current_balance');
                    echo $this->Form->control('WBAID');
                    echo $this->Form->control('API_VERSION');
                    echo $this->Form->control('ACCESSTOKENVALUE');
                    echo $this->Form->control('phone_number_id');
                    echo $this->Form->control('def_language');
                    echo $this->Form->control('test_number');
                    echo $this->Form->control('restricted_start_time');
                    echo $this->Form->control('restricted_end_time');
                    echo $this->Form->control('interactive_webhook');
                    echo $this->Form->control('rcv_notification_template');
                    echo $this->Form->control('interactive_api_key');
                    echo $this->Form->control('interactive_menu_function');
                    echo $this->Form->control('interactive_notification_numbers');
                    echo $this->Form->control('def_isd');
                    echo $this->Form->control('welcome_msg');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
