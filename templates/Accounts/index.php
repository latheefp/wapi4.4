
  <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Accounts</h1>
                </div>
                <div class="col-sm-6">
                    <?= $this->Html->link(__('New Account'), ['action' => 'add'], ['class' => 'btn btn-primary float-right']) ?>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th><?= $this->Paginator->sort('id') ?></th>
                                    <th><?= $this->Paginator->sort('company_name') ?></th>
                                    <th><?= $this->Paginator->sort('primary_contact_person') ?></th>
                                    <th><?= $this->Paginator->sort('primary_number') ?></th>
                                    <th><?= $this->Paginator->sort('current_balance') ?></th>
                                    <th><?= $this->Paginator->sort('WBAID') ?></th>
                                    <th><?= $this->Paginator->sort('phone_numberId') ?></th>
                            
                                    <th><?= $this->Paginator->sort('def_isd') ?></th>
                                    <th class="actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td><?= $this->Number->format($account->id) ?></td>
                                    <td><?= h($account->company_name) ?></td>
                                    <td><?= h($account->primary_contact_person) ?></td>
                                    <td><?= h($account->primary_number) ?></td>
                                    
                                    
                                    <td><?= $this->Number->format($account->current_balance) ?></td>
                                    <td><?= h($account->WBAID) ?></td>
                            
                                    <td><?= h($account->phone_numberId) ?></td>
                                  
                                    <td><?= h($account->def_isd) ?></td>
                                    <td class="actions">
                                        <?= $this->Html->link(__('View'), ['action' => 'view', $account->id], ['class' => 'btn btn-sm btn-info']) ?>
                                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $account->id], ['class' => 'btn btn-sm btn-primary']) ?>
                                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $account->id], ['confirm' => __('Are you sure you want to delete # {0}?', $account->id), 'class' => 'btn btn-sm btn-danger']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
                        </div>
                        <ul class="pagination">
                            <?= $this->Paginator->first('<<', ['class' => 'page-link']) ?>
                            <?= $this->Paginator->prev('<', ['class' => 'page-link']) ?>
                            <?= $this->Paginator->numbers(['class' => 'page-link']) ?>
                            <?= $this->Paginator->next('>', ['class' => 'page-link']) ?>
                            <?= $this->Paginator->last('>>', ['class' => 'page-link']) ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- </div> -->