<div class="col-md-12">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Edit Account Details</h3>
        </div>
        <?= $this->Form->create($account, ['class' => 'form-horizontal', 'id' => 'account-form']) ?>
        <div class="card-body">
            <!-- First Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('company_name', [
                            'class' => 'form-control',
                            'label' => 'Company Name <small class="text-muted">(Required)</small>',
                            'escape' => false
                        ]) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('Address', [
                            'class' => 'form-control',
                            'templateVars' => ['icon' => '<i class="fas fa-map-marker-alt"></i>']
                        ]) ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <!-- Contact Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('primary_contact_person', [
                                    'class' => 'form-control',
                                    'templateVars' => ['icon' => '<i class="fas fa-user"></i>']
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('primary_number', [
                                    'class' => 'form-control',
                                    'templateVars' => ['icon' => '<i class="fas fa-phone"></i>']
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('secondary_number', [
                            'class' => 'form-control',
                            'templateVars' => ['icon' => '<i class="fas fa-phone-alt"></i>']
                        ]) ?>
                    </div>
                </div>
            </div>
            
            <!-- Second Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('current_balance', [
                            'class' => 'form-control',
                            'append' => '<i class="fas fa-dollar-sign"></i>'
                        ]) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('WBAID', ['class' => 'form-control']) ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('API_VERSION', ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('phone_numberId', ['class' => 'form-control']) ?>
                    </div>
                </div>
            </div>
            
            <!-- Time Range Picker -->
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
            
            <!-- Other Fields -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('def_language', [
                            'class' => 'form-control select2',
                            'options' => [
                                'en' => 'English',
                                'ar' => 'Arabic',
                                'fr' => 'French'
                            ]
                        ]) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('test_number', ['class' => 'form-control']) ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('def_isd', ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->control('interactive_notification_numbers', [
                            'class' => 'form-control',
                            'templateVars' => ['help' => 'Comma separated numbers']
                        ]) ?>
                    </div>
                </div>
            </div>
            
            <!-- Countries Selection -->
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
                </div>
            </div>
        </div>
        
        <div class="card-footer">
            <?= $this->Form->button('<i class="fas fa-save"></i> Save Changes', [
                'class' => 'btn btn-primary float-right',
                'type' => 'submit',
                'escape' => false
            ]) ?>
            <?= $this->Html->link('<i class="fas fa-times"></i> Cancel', ['action' => 'index'], [
                'class' => 'btn btn-default',
                'escape' => false
            ]) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<?php $this->Html->scriptStart(['block' => true]); ?>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: 'Select options',
            allowClear: true,
            width: '100%'
        });

        // Initialize Time Range Picker
        $('#time-range').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 15,
            locale: {
                format: 'HH:mm'
            },
            startDate: '<?= $account->restricted_start_time ?>',
            endDate: '<?= $account->restricted_end_time ?>'
        }).on('apply.daterangepicker', function(ev, picker) {
            $('#restricted_start_time').val(picker.startDate.format('HH:mm:ss'));
            $('#restricted_end_time').val(picker.endDate.format('HH:mm:ss'));
        });

        // Initialize Datepicker if needed
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
<?php $this->Html->scriptEnd(); ?>