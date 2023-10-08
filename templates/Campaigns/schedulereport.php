<?php
//debug($feildsType);
$this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']],
    ['title' => 'Templates', 'url' => ['controller' => 'Templates', 'action' => 'index']]
]);
?>
<table id="schedreporttable" class="table table-striped table-bordered dt-responsive nowrap dtselect" style="width:100%">
    <thead>
        <tr>
            <?php
            foreach ($feildsType as $key => $val) {
                if ($val['viewable'] == true) {
//                             print '<th>'.$key.'</th>';
                    print '<th>' . $val['title'] . '</th>';
                }
            }
            ?>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
    </tfoot>
</table>



<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
    $(function () {
        var table = $('#schedreporttable').DataTable({
            "ajax": {
                "url": "/campaigns/getschedulereport",
                "type": "POST",
                "data": function (d) {
                    d.schedule_id = <?= $id ?>;

                },
                beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
            },
            lengthChange: false,
            "stateSave": true,
            "lengthMenu": [[5, 10, 15, 25, 50, 100], [5, 10, 15, 25, 50, 100]],
            "processing": true,
            "serverSide": true,
            "pageLength": <?php print $PageLength; ?>,
            scrollX: "300px",
            scrollCollapse: true,
            select: true,
            "columns": [
<?php
foreach ($feildsType as $key => $val) {
    if ($val['viewable'] == true) {
        if ($val['searchable'] == 1) {
            $searchable = "true";
        } else {
            $searchable = "false";
        }
        print '{"data":"' . $val['title'] . '", "name":"' . $val['fld_name'] . '", "width":"' . $val['width'] . '%",' . '"searchable":' . $searchable . '},' . "\n";
    }
}
?>

            ],
        }
        ); //End of dT.


    })


    //</script>
<?php $this->Html->scriptEnd(); ?>