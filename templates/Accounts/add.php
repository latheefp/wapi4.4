<div class="col-md-12">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Edit Account Details</h3>
        </div>
        <?= $this->Form->create($account, ['class' => 'form-horizontal', 'id' => 'account-form']) ?>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('company_name', [
                            'class' => 'form-control',
                            'templateVars' => ['help' => 'The official company name']
                        ]) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('Address', ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('primary_contact_person', ['class' => 'form-control']) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('primary_number', ['class' => 'form-control']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('secondary_number', ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('current_balance', ['class' => 'form-control']) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('WBAID', ['class' => 'form-control']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('API_VERSION', ['class' => 'form-control']) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('phone_numberId', ['class' => 'form-control']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('ACCESSTOKENVALUE', ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('def_language', ['class' => 'form-control']) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('test_number', ['class' => 'form-control']) ?>
                            </div>
                        </div>
                    </div>

                      <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Restricted Time Range</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-clock"></i>
                                </span>
                            </div>
                            <?= $this->Form->text('time_range', [
                                'class' => 'form-control float-right',
                                'id' => 'time-range',
                                'value' => $account->restricted_start_time . ' - ' . $account->restricted_end_time
                            ]) ?>
                            <?= $this->Form->hidden('restricted_start_time', ['id' => 'restricted_start_time']) ?>
                            <?= $this->Form->hidden('restricted_end_time', ['id' => 'restricted_end_time']) ?>
                        </div>
                    </div>
                </div>
            </div>
                    
                    <!-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('restricted_start_time', [
                                    'class' => 'form-control timepicker',
                                    'type' => 'text'
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('restricted_end_time', [
                                    'class' => 'form-control timepicker',
                                    'type' => 'text'
                                ]) ?>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('interactive_webhook', ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('rcv_notification_template', ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('interactive_api_key', ['class' => 'form-control']) ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('interactive_menu_function', ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('interactive_notification_numbers', ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('def_isd', ['class' => 'form-control']) ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= $this->Form->control('countries._ids', [
                            'type' => 'select',
                            'multiple' => true,
                            'options' => $countries,
                            'label' => 'Associated Countries',
                            'class' => 'select2',
                            'value' => $account->countries ? array_column($account->countries, 'id') : [],
                            'id' => 'countries-ids',
                            'style' => 'width: 100%'
                        ]) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('welcome_msg', [
                            'class' => 'form-control',
                            'type' => 'textarea',
                            'rows' => 3
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <?= $this->Form->button(__('Save Changes'), [
                'class' => 'btn btn-primary float-right',
                'type' => 'submit'
            ]) ?>
            <?= $this->Html->link(__('Cancel'), ['action' => 'index'], [
                'class' => 'btn btn-default'
            ]) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<?php $this->Html->scriptStart(['block' => true]); 


$defaultStart = (empty($account->restricted_start_time)) ? date('H:i') : $account->restricted_start_time;
$defaultEnd = (empty($account->restricted_end_time)) ? date('H:i', strtotime('+1 hour')) : $account->restricted_end_time;


?>
//<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: 'Select countries',
            allowClear: true,
            width: '100%',
            closeOnSelect: false
        });


          $('#time-range').daterangepicker({
            timePicker: true,
            // showCalendar: false,  // Hide the calendar
            // showCustomRangeLabel: false,
            // alwaysShowCalendars: false,
            timePicker24Hour: true,
            timePickerIncrement: 15,
            locale: {
                format: 'HH:mm'
            },
            startDate: '<?= $defaultStart ?>',
            endDate: '<?= $defaultEnd ?>'
        }).on('apply.daterangepicker', function(ev, picker) {
            $('#restricted_start_time').val(picker.startDate.format('HH:mm:ss'));
            $('#restricted_end_time').val(picker.endDate.format('HH:mm:ss'));
        });
    });
//</script>
<?php $this->Html->scriptEnd(); ?>