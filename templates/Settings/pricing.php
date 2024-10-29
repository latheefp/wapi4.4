<?php
//debug($prices);
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $doctors
 */
?>
<?php
$this->assign('title', __('Contact Streams'));
$this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => '/'],
    ['title' => 'List Contacts'],
]);
?>

<div class="card card-primary card-outline">
    <div class="card-header d-sm-flex">
        <h2 class="card-title">
            <!-- -->
        </h2>
        <div class="card-toolbox d-sm-flex align-items-center">
            <?= $this->Paginator->limitControl([], null, [
                'label' => false,
                'class' => 'form-control-sm',
            ]); ?>
            
            <!-- Search Form -->
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-sm-flex me-2']) ?>
            <?= $this->Form->control('q', [
                'label' => false,
                'placeholder' => 'Search...',
                'class' => 'form-control form-control-sm me-2',
                'value' => $query
            ]) ?>
            
          
            <?= $this->Form->button(__('Search'), ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Form->end() ?>
            
            
             <!-- File Upload Button -->
            <div class="ms-2">
                <?= $this->Form->create(null, [
                    'type' => 'file',
                    'id' => 'cost-upload-form', // Add form ID for AJAX targeting
                    'class' => 'd-sm-flex',
                    'url' => ['controller' => 'Settings', 'action' => 'costupload', '_ext' => 'json'] // set the action to the upload URL and request JSON response
                ]) ?>
                 <!-- File Type Selection -->
                <?= $this->Form->control('file_type', [
                    'type' => 'select',
                    'options' => [
                        'price_chart' => 'Price Chart',
                        'country_mapping' => 'Country Mapping'
                    ],
                    'label' => false,
                    'class' => 'form-select form-select-sm me-2', // Small dropdown style
                    'empty' => 'Select File Type', // Optional placeholder
                    'id' => 'file_type' // Add ID for JS targeting if needed
                ]) ?>
                <?= $this->Form->control('upload_file', [
                    'type' => 'file',
                    'label' => false,
                    'title' => "Fileformat is: Market,Currency,Marketing,Utility,Authentication,Authentication-International,Service" .
                                "https://developers.facebook.com/docs/whatsapp/pricing#rate-cards",
                    'class' => 'form-control form-control-sm me-2',
                    'id' => 'upload_file' // Add ID for targeting via JS
                ]) ?>
                <?= $this->Form->button(__('Upload Pricing'), ['class' => 'btn btn-sm btn-outline-primary', 'type' => 'button', 'onclick' => 'uploadFile()']) ?>
                <?= $this->Form->end() ?>
            </div>

            
            <!-- Add New Button -->
            
        </div>
    </div>
    
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('country') ?></th>
                    <th><?= $this->Paginator->sort('country_code') ?></th>
                    <th><?= $this->Paginator->sort('marketing') ?></th>
                    <th><?= $this->Paginator->sort('utility') ?></th>
                    <th><?= $this->Paginator->sort('authentication') ?></th>
                    <th><?= $this->Paginator->sort('service') ?></th>
                    <th><?= $this->Paginator->sort('business_Initiated_rate') ?></th>
                    <th><?= $this->Paginator->sort('user_Initiated_rate') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>                    
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prices as $price) : ?>
                    <tr id="contact-row-<?= $price->id ?>">
                        <td><?= $this->Number->format(number: $price->id) ?></td>
                        <td><?= h($price->country) ?></td>
                        <td><?= h($price->country_code) ?></td>
                        <td><?= h($price->marketing) ?></td>
                        <td><?= h($price->utility) ?></td>
                        <td><?= h($price->authentication) ?></td>
                        <td><?= h($price->service) ?></td>
                       
                        <td><?= h($price->business_Initiated_rate) ?></td>
                        <td><?= h($price->user_Initiated_rate) ?></td>
                        <td><?= h($price->modified) ?></td>
                        <td class="actions" id="actions">
                            <button class="btn btn-xs btn-outline-danger" onClick="unblockContact(<?= $price->id ?>)">
                                    <?= __('Delete') ?>
                                </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->

    <div class="card-footer d-md-flex paginator">
        <div class="mr-auto" style="font-size:.8rem">
            <?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?>
        </div>
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->first('<i class="fas fa-angle-double-left"></i>', ['escape' => false]) ?>
            <?= $this->Paginator->prev('<i class="fas fa-angle-left"></i>', ['escape' => false]) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next('<i class="fas fa-angle-right"></i>', ['escape' => false]) ?>
            <?= $this->Paginator->last('<i class="fas fa-angle-double-right"></i>', ['escape' => false]) ?>
        </ul>
    </div>
    <!-- /.card-footer -->
</div>


<script>

// function unblockContact(contactId) {
//     if (confirm('Are you sure you want to unblock this contact?')) {
//         $.ajax({
//             beforeSend: function(xhr) {
//                 xhr.setRequestHeader('X-CSRF-Token', csrfToken);  // Attach CSRF token for security
//             },
//             url: "/contacts/unblocknumber/" + contactId,  // Make the AJAX call to unblock the contact
//             method: "DELETE",  // Use the DELETE method for unblocking/removal
//             success: function(data) {
//                 if (data.status === 'success') {
//                     toastr.success(data.message);  // Show success message using Toastr
//                     // Update the block status icon instead of removing the row
//                     $('#contact-row-' + contactId).find('#blockstatus').html('<i class="fas fa-check-circle" style="color: green;" title="Unblocked"></i>');
//                     $('#contact-row-' + contactId).find('#actions').html('<button class="btn btn-xs btn-outline-danger" onclick="blocknumber('+contactId+')">Block</button>');
//                 } else {
//                     toastr.error(data.message);  // Show error message if something went wrong
//                 }
//             },
//             error: function(jqXHR, textStatus, errorThrown) {
//                 toastr.error('An error occurred while processing the request.');  // Handle error
//             }
//         });
//     }
// }

// function blocknumber(contactId) {
//             var table = $('#tablecampaign').DataTable();
//             var id = table.row('.selected').id();

//             $.ajax({
//                 beforeSend: function(xhr) {
//                     xhr.setRequestHeader('X-CSRF-Token', csrfToken);  // Attach CSRF token for security
//                 },
//                 url: "/contacts/blocknumber/" + contactId,  // Make the AJAX call to block the number
//                 method: "GET",
//                 success: function(data) {
//                     if (data.status === 'success') {
//                         $('#contact-row-' + contactId).find('#blockstatus').html('<i class="fas fa-check-circle" style="color: green;" title="Unblocked"></i>');
//                         $('#contact-row-' + contactId).find('#actions').html('<button class="btn btn-xs btn-outline-danger" onclick="blocknumber('+contactId+')">Unblock</button>');
//                         toastr.success(data.message);
//                     } else if (data.status === 'error') {
//                         // Display error message using Toastr
//                         toastr.error(data.message);
//                     }
//                 },
//                 error: function(jqXHR, textStatus, errorThrown) {
//                     // Handle any errors that might occur during the AJAX request itself
//                     toastr.error('An error occurred while processing the request.');
//                 }
//             });
//         }


function uploadFile() {
    var formData = new FormData(document.getElementById('cost-upload-form'));
    const fileType = document.getElementById('file_type').value;
    // Perform AJAX request
    if (!fileType) {
        alert('Please select a file type.');
        return;
    }
    formData.append('file_type', fileType);
    $.ajax({
        url: '/settings/costupload', // Action URL
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            // Assuming the server returns JSON response with a message
            if (response.success) {
                toastr.success('File uploaded successfully!');
            } else {
                toastr.error('Failed to upload file: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            toastr.error('An error occurred: ' + error);
        }
    });
}


</script>