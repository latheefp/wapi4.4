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
            <?= $this->Html->link(__('Edit Account'), ['action' => 'edit', $account->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Account'), ['action' => 'delete', $account->id], ['confirm' => __('Are you sure you want to delete # {0}?', $account->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Accounts'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Account'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="accounts view content">
            <h3><?= h($account->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Company Name') ?></th>
                    <td><?= h($account->company_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Primary Contact Person') ?></th>
                    <td><?= h($account->primary_contact_person) ?></td>
                </tr>
                <tr>
                    <th><?= __('Primary Number') ?></th>
                    <td><?= h($account->primary_number) ?></td>
                </tr>
                <tr>
                    <th><?= __('Secondary Number') ?></th>
                    <td><?= h($account->secondary_number) ?></td>
                </tr>
                <tr>
                    <th><?= __('WBAID') ?></th>
                    <td><?= h($account->WBAID) ?></td>
                </tr>
                <tr>
                    <th><?= __('API VERSION') ?></th>
                    <td><?= h($account->API_VERSION) ?></td>
                </tr>
                <tr>
                    <th><?= __('ACCESSTOKENVALUE') ?></th>
                    <td><?= h($account->ACCESSTOKENVALUE) ?></td>
                </tr>
                <tr>
                    <th><?= __('Phone Number Id') ?></th>
                    <td><?= h($account->phone_number_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Def Language') ?></th>
                    <td><?= h($account->def_language) ?></td>
                </tr>
                <tr>
                    <th><?= __('Test Number') ?></th>
                    <td><?= h($account->test_number) ?></td>
                </tr>
                <tr>
                    <th><?= __('Interactive Webhook') ?></th>
                    <td><?= h($account->interactive_webhook) ?></td>
                </tr>
                <tr>
                    <th><?= __('Rcv Notification Template') ?></th>
                    <td><?= h($account->rcv_notification_template) ?></td>
                </tr>
                <tr>
                    <th><?= __('Interactive Api Key') ?></th>
                    <td><?= h($account->interactive_api_key) ?></td>
                </tr>
                <tr>
                    <th><?= __('Interactive Menu Function') ?></th>
                    <td><?= h($account->interactive_menu_function) ?></td>
                </tr>
                <tr>
                    <th><?= __('Interactive Notification Numbers') ?></th>
                    <td><?= h($account->interactive_notification_numbers) ?></td>
                </tr>
                <tr>
                    <th><?= __('Def Isd') ?></th>
                    <td><?= h($account->def_isd) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($account->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('User Id') ?></th>
                    <td><?= $this->Number->format($account->user_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Current Balance') ?></th>
                    <td><?= $this->Number->format($account->current_balance) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($account->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($account->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Restricted Start Time') ?></th>
                    <td><?= h($account->restricted_start_time) ?></td>
                </tr>
                <tr>
                    <th><?= __('Restricted End Time') ?></th>
                    <td><?= h($account->restricted_end_time) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Address') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($account->Address)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Welcome Msg') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($account->welcome_msg)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Users') ?></h4>
                <?php if (!empty($account->users)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Last Logged') ?></th>
                            <th><?= __('Show Closed') ?></th>
                            <th><?= __('Ugroup Id') ?></th>
                            <th><?= __('Account Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Username') ?></th>
                            <th><?= __('Password') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Mobile Number') ?></th>
                            <th><?= __('Active') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Show Cols') ?></th>
                            <th><?= __('Login Count') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($account->users as $users) : ?>
                        <tr>
                            <td><?= h($users->id) ?></td>
                            <td><?= h($users->last_logged) ?></td>
                            <td><?= h($users->show_closed) ?></td>
                            <td><?= h($users->ugroup_id) ?></td>
                            <td><?= h($users->account_id) ?></td>
                            <td><?= h($users->name) ?></td>
                            <td><?= h($users->username) ?></td>
                            <td><?= h($users->password) ?></td>
                            <td><?= h($users->email) ?></td>
                            <td><?= h($users->mobile_number) ?></td>
                            <td><?= h($users->active) ?></td>
                            <td><?= h($users->created) ?></td>
                            <td><?= h($users->modified) ?></td>
                            <td><?= h($users->show_cols) ?></td>
                            <td><?= h($users->login_count) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Users', 'action' => 'view', $users->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'edit', $users->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Users', 'action' => 'delete', $users->id], ['confirm' => __('Are you sure you want to delete # {0}?', $users->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Api Keys') ?></h4>
                <?php if (!empty($account->api_keys)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Api Name') ?></th>
                            <th><?= __('Api Key') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Enabled') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Ip List') ?></th>
                            <th><?= __('Account Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($account->api_keys as $apiKeys) : ?>
                        <tr>
                            <td><?= h($apiKeys->id) ?></td>
                            <td><?= h($apiKeys->api_name) ?></td>
                            <td><?= h($apiKeys->api_key) ?></td>
                            <td><?= h($apiKeys->user_id) ?></td>
                            <td><?= h($apiKeys->enabled) ?></td>
                            <td><?= h($apiKeys->created) ?></td>
                            <td><?= h($apiKeys->ip_list) ?></td>
                            <td><?= h($apiKeys->account_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'ApiKeys', 'action' => 'view', $apiKeys->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'ApiKeys', 'action' => 'edit', $apiKeys->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'ApiKeys', 'action' => 'delete', $apiKeys->id], ['confirm' => __('Are you sure you want to delete # {0}?', $apiKeys->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Campaign Views') ?></h4>
                <?php if (!empty($account->campaign_views)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Campaign Name') ?></th>
                            <th><?= __('Start Date') ?></th>
                            <th><?= __('End Date') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Template Id') ?></th>
                            <th><?= __('Template') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Account Id') ?></th>
                            <th><?= __('Company Name') ?></th>
                            <th><?= __('User') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($account->campaign_views as $campaignViews) : ?>
                        <tr>
                            <td><?= h($campaignViews->id) ?></td>
                            <td><?= h($campaignViews->campaign_name) ?></td>
                            <td><?= h($campaignViews->start_date) ?></td>
                            <td><?= h($campaignViews->end_date) ?></td>
                            <td><?= h($campaignViews->created) ?></td>
                            <td><?= h($campaignViews->user_id) ?></td>
                            <td><?= h($campaignViews->template_id) ?></td>
                            <td><?= h($campaignViews->template) ?></td>
                            <td><?= h($campaignViews->status) ?></td>
                            <td><?= h($campaignViews->account_id) ?></td>
                            <td><?= h($campaignViews->company_name) ?></td>
                            <td><?= h($campaignViews->user) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'CampaignViews', 'action' => 'view', $campaignViews->]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'CampaignViews', 'action' => 'edit', $campaignViews->]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'CampaignViews', 'action' => 'delete', $campaignViews->], ['confirm' => __('Are you sure you want to delete # {0}?', $campaignViews->)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Schedule Views') ?></h4>
                <?php if (!empty($account->schedule_views)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Campaign Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Template') ?></th>
                            <th><?= __('Template Status') ?></th>
                            <th><?= __('Account Id') ?></th>
                            <th><?= __('Campaign Name') ?></th>
                            <th><?= __('Start Date') ?></th>
                            <th><?= __('End Date') ?></th>
                            <th><?= __('User') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($account->schedule_views as $scheduleViews) : ?>
                        <tr>
                            <td><?= h($scheduleViews->id) ?></td>
                            <td><?= h($scheduleViews->name) ?></td>
                            <td><?= h($scheduleViews->campaign_id) ?></td>
                            <td><?= h($scheduleViews->user_id) ?></td>
                            <td><?= h($scheduleViews->created) ?></td>
                            <td><?= h($scheduleViews->status) ?></td>
                            <td><?= h($scheduleViews->template) ?></td>
                            <td><?= h($scheduleViews->template_status) ?></td>
                            <td><?= h($scheduleViews->account_id) ?></td>
                            <td><?= h($scheduleViews->campaign_name) ?></td>
                            <td><?= h($scheduleViews->start_date) ?></td>
                            <td><?= h($scheduleViews->end_date) ?></td>
                            <td><?= h($scheduleViews->user) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'ScheduleViews', 'action' => 'view', $scheduleViews->]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'ScheduleViews', 'action' => 'edit', $scheduleViews->]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'ScheduleViews', 'action' => 'delete', $scheduleViews->], ['confirm' => __('Are you sure you want to delete # {0}?', $scheduleViews->)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Streams') ?></h4>
                <?php if (!empty($account->streams)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Hookid') ?></th>
                            <th><?= __('Messaging Product') ?></th>
                            <th><?= __('Display Phone Number') ?></th>
                            <th><?= __('Phonenumberid') ?></th>
                            <th><?= __('Contact Stream Id') ?></th>
                            <th><?= __('Schedule Id') ?></th>
                            <th><?= __('Lang') ?></th>
                            <th><?= __('Message Context From') ?></th>
                            <th><?= __('Message From') ?></th>
                            <th><?= __('Message Timestamp') ?></th>
                            <th><?= __('Message Txt Body') ?></th>
                            <th><?= __('Message Context') ?></th>
                            <th><?= __('Message ContextId') ?></th>
                            <th><?= __('Message ContextFrom') ?></th>
                            <th><?= __('Messageid') ?></th>
                            <th><?= __('Initiator') ?></th>
                            <th><?= __('Replyid') ?></th>
                            <th><?= __('Timestamp') ?></th>
                            <th><?= __('Type') ?></th>
                            <th><?= __('Has Wa') ?></th>
                            <th><?= __('Message Format Type') ?></th>
                            <th><?= __('Read Time') ?></th>
                            <th><?= __('Delivered Time') ?></th>
                            <th><?= __('Sent Time') ?></th>
                            <th><?= __('Button Payload') ?></th>
                            <th><?= __('Button Text') ?></th>
                            <th><?= __('Sendarray') ?></th>
                            <th><?= __('Postdata') ?></th>
                            <th><?= __('Recievearray') ?></th>
                            <th><?= __('Result') ?></th>
                            <th><?= __('Billable') ?></th>
                            <th><?= __('Pricing Model') ?></th>
                            <th><?= __('Costed') ?></th>
                            <th><?= __('Rated') ?></th>
                            <th><?= __('Category') ?></th>
                            <th><?= __('Success') ?></th>
                            <th><?= __('Errors') ?></th>
                            <th><?= __('Commented') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Conversationid') ?></th>
                            <th><?= __('Account Id') ?></th>
                            <th><?= __('Conversation Expiration Timestamp') ?></th>
                            <th><?= __('Conversation Origin Type') ?></th>
                            <th><?= __('Tmp Upate Json') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($account->streams as $streams) : ?>
                        <tr>
                            <td><?= h($streams->id) ?></td>
                            <td><?= h($streams->hookid) ?></td>
                            <td><?= h($streams->messaging_product) ?></td>
                            <td><?= h($streams->display_phone_number) ?></td>
                            <td><?= h($streams->phonenumberid) ?></td>
                            <td><?= h($streams->contact_stream_id) ?></td>
                            <td><?= h($streams->schedule_id) ?></td>
                            <td><?= h($streams->lang) ?></td>
                            <td><?= h($streams->message_context_from) ?></td>
                            <td><?= h($streams->message_from) ?></td>
                            <td><?= h($streams->message_timestamp) ?></td>
                            <td><?= h($streams->message_txt_body) ?></td>
                            <td><?= h($streams->message_context) ?></td>
                            <td><?= h($streams->message_contextId) ?></td>
                            <td><?= h($streams->message_contextFrom) ?></td>
                            <td><?= h($streams->messageid) ?></td>
                            <td><?= h($streams->initiator) ?></td>
                            <td><?= h($streams->replyid) ?></td>
                            <td><?= h($streams->timestamp) ?></td>
                            <td><?= h($streams->type) ?></td>
                            <td><?= h($streams->has_wa) ?></td>
                            <td><?= h($streams->message_format_type) ?></td>
                            <td><?= h($streams->read_time) ?></td>
                            <td><?= h($streams->delivered_time) ?></td>
                            <td><?= h($streams->sent_time) ?></td>
                            <td><?= h($streams->button_payload) ?></td>
                            <td><?= h($streams->button_text) ?></td>
                            <td><?= h($streams->sendarray) ?></td>
                            <td><?= h($streams->postdata) ?></td>
                            <td><?= h($streams->recievearray) ?></td>
                            <td><?= h($streams->result) ?></td>
                            <td><?= h($streams->billable) ?></td>
                            <td><?= h($streams->pricing_model) ?></td>
                            <td><?= h($streams->costed) ?></td>
                            <td><?= h($streams->rated) ?></td>
                            <td><?= h($streams->category) ?></td>
                            <td><?= h($streams->success) ?></td>
                            <td><?= h($streams->errors) ?></td>
                            <td><?= h($streams->commented) ?></td>
                            <td><?= h($streams->created) ?></td>
                            <td><?= h($streams->modified) ?></td>
                            <td><?= h($streams->conversationid) ?></td>
                            <td><?= h($streams->account_id) ?></td>
                            <td><?= h($streams->conversation_expiration_timestamp) ?></td>
                            <td><?= h($streams->conversation_origin_type) ?></td>
                            <td><?= h($streams->tmp_upate_json) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Streams', 'action' => 'view', $streams->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Streams', 'action' => 'edit', $streams->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Streams', 'action' => 'delete', $streams->id], ['confirm' => __('Are you sure you want to delete # {0}?', $streams->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Templates') ?></h4>
                <?php if (!empty($account->templates)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Language') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Active') ?></th>
                            <th><?= __('Template Details') ?></th>
                            <th><?= __('Account Id') ?></th>
                            <th><?= __('Category') ?></th>
                            <th><?= __('Created') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($account->templates as $templates) : ?>
                        <tr>
                            <td><?= h($templates->id) ?></td>
                            <td><?= h($templates->name) ?></td>
                            <td><?= h($templates->language) ?></td>
                            <td><?= h($templates->status) ?></td>
                            <td><?= h($templates->active) ?></td>
                            <td><?= h($templates->template_details) ?></td>
                            <td><?= h($templates->account_id) ?></td>
                            <td><?= h($templates->category) ?></td>
                            <td><?= h($templates->created) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Templates', 'action' => 'view', $templates->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Templates', 'action' => 'edit', $templates->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Templates', 'action' => 'delete', $templates->id], ['confirm' => __('Are you sure you want to delete # {0}?', $templates->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Ugroups Permissions') ?></h4>
                <?php if (!empty($account->ugroups_permissions)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Ugroup Id') ?></th>
                            <th><?= __('Permission Id') ?></th>
                            <th><?= __('Account Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($account->ugroups_permissions as $ugroupsPermissions) : ?>
                        <tr>
                            <td><?= h($ugroupsPermissions->id) ?></td>
                            <td><?= h($ugroupsPermissions->ugroup_id) ?></td>
                            <td><?= h($ugroupsPermissions->permission_id) ?></td>
                            <td><?= h($ugroupsPermissions->account_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'UgroupsPermissions', 'action' => 'view', $ugroupsPermissions->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'UgroupsPermissions', 'action' => 'edit', $ugroupsPermissions->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'UgroupsPermissions', 'action' => 'delete', $ugroupsPermissions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $ugroupsPermissions->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
