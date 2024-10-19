<?php
//debug($numbers);
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
            
            <?= $this->Form->select('status', [
                'all' => 'All',
                'blocked' => 'Blocked',
                'unblocked' => 'Unblocked'
            ], ['default' => 'all', 'class' => 'form-control form-control-sm me-2']) ?>
            
            <?= $this->Form->button(__('Search'), ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        <?= $this->Form->end() ?>

            
            <!-- Add New Button -->
            
        </div>
    </div>
    




    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('contact_number') ?></th>
                    <th><?= $this->Paginator->sort('profile') ?></th>
                    <th><?= $this->Paginator->sort('camp_blocked') ?></th>
                    <th><?= $this->Paginator->sort('user') ?></th>
                  
                    <th><?= $this->Paginator->sort('created') ?></th>                    
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($numbers as $number) : ?>
                    <tr id="contact-row-<?= $number->id ?>">
                        <td><?= $this->Number->format($number->id) ?></td>
                        <td><?= h($number->contact_number) ?></td>
                        <td><?= h($number->profile_name) ?></td>
                        <td id="blockstatus">
                            <?php if ($number->camp_blocked): ?>
                                <i class="fas fa-ban" style="color: red;" title="Blocked"></i> <!-- Blocked (Banned) -->
                            <?php else: ?>
                                <i class="fas fa-check-circle" style="color: green;" title="Unblocked"></i> <!-- Unblocked -->
                            <?php endif; ?>
                        </td>
                        <td> <?php if(isset($number->user->name)) {echo $number->user->name;} ?></td>
                        <td><?= $number->created ?></td>
                        <td class="actions" id="actions">

                        <?php if ($number->camp_blocked): ?>
                            <button class="btn btn-xs btn-outline-danger" onClick="unblockContact(<?= $number->id ?>)">
                                    <?= __('Unblock') ?>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-xs btn-outline-danger" onClick="blocknumber(<?= $number->id ?>)">
                                    <?= __('Block') ?>
                                </button>
                            <?php endif; ?>
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

function unblockContact(contactId) {
    if (confirm('Are you sure you want to unblock this contact?')) {
        $.ajax({
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);  // Attach CSRF token for security
            },
            url: "/contacts/unblocknumber/" + contactId,  // Make the AJAX call to unblock the contact
            method: "DELETE",  // Use the DELETE method for unblocking/removal
            success: function(data) {
                if (data.status === 'success') {
                    toastr.success(data.message);  // Show success message using Toastr
                    // Update the block status icon instead of removing the row
                    $('#contact-row-' + contactId).find('#blockstatus').html('<i class="fas fa-check-circle" style="color: green;" title="Unblocked"></i>');
                    $('#contact-row-' + contactId).find('#actions').html('<button class="btn btn-xs btn-outline-danger" onclick="blocknumber('+contactId+')">Block</button>');
                } else {
                    toastr.error(data.message);  // Show error message if something went wrong
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error('An error occurred while processing the request.');  // Handle error
            }
        });
    }
}

function blocknumber(contactId) {
            var table = $('#tablecampaign').DataTable();
            var id = table.row('.selected').id();

            $.ajax({
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);  // Attach CSRF token for security
                },
                url: "/contacts/blocknumber/" + contactId,  // Make the AJAX call to block the number
                method: "GET",
                success: function(data) {
                    if (data.status === 'success') {
                        $('#contact-row-' + contactId).find('#blockstatus').html('<i class="fas fa-check-circle" style="color: green;" title="Unblocked"></i>');
                        $('#contact-row-' + contactId).find('#actions').html('<button class="btn btn-xs btn-outline-danger" onclick="blocknumber('+contactId+')">Unblock</button>');
                        toastr.success(data.message);
                    } else if (data.status === 'error') {
                        // Display error message using Toastr
                        toastr.error(data.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle any errors that might occur during the AJAX request itself
                    toastr.error('An error occurred while processing the request.');
                }
            });
        }



</script>