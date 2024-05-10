<?php
// debug($feildsType);
$this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']],
    ['title' => 'Invoices', 'url' => ['controller' => 'Invoice', 'action' => 'index']]
]);
?>
<table id="tableinvoice" class="table table-striped table-bordered dt-responsive nowrap dtselect" style="width:100%">
    <thead>
        <tr>
            <th>Mobile Number</th>
            <th>Profile</th>
            <th>Name</th>
            <th>Country</th>
            <th>Initiator</th>
            <th>Type</th>
            <th>Schedule</th>
            <th>Pricing Model</th>
            <th>Created</th>
            <th>Cost</th>
            <th>Sub Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
         $total=0;
        foreach ($data as $key => $val) { 
          //  debug($val);
            $total= $total+$val['cost'];
            ?>
            <tr>
                <td><?= $val['stream']['contact_stream']['contact_number'] ?></td>
                <td><?= $val['stream']['contact_stream']['profile'] ?></td>
                <td><?= $val['stream']['contact_stream']['name'] ?></td>
                <td><?= $val['country']?></td>
                <td><?= $val['stream']['initiator'] ?></td>
                <td><?= $val['stream']['type'] ?></td>
                <td><?php if(isset($val['stream']['schedule']['name'])){ echo $val['stream']['schedule']['name']; } ?></td>
                <td><?= $val['stream']['pricing_model'] ?></td>
                <td><?= $val['stream']['created'] ?></td>
                <td><?= $val['cost']?></td>
                <td><?= $total?></td>
            </tr>


        <?php } ?>
    </tbody>
    <tfoot>
    </tfoot>
</table>