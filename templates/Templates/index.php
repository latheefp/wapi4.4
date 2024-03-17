<?php
//debug($feildsType);
$this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']],
    ['title' => 'Templates', 'url' => ['controller' => 'Templates', 'action' => 'index']]
]);
?>
<table id="tabletemplates" class="table table-striped table-bordered dt-responsive nowrap dtselect" style="width:100%">
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
    $(function() {


        var table = $('#tabletemplates').DataTable({
            "ajax": {
                "url": "/templates/gettemplates",
                "type": "POST",
                beforeSend: function(xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
            },
            //        lengthChange: false,        
            "stateSave": true,
            "lengthMenu": [
                [5, 10, 15, 25, 50, 100],
                [5, 10, 15, 25, 50, 100]
            ],
             "processing": true,
            "serverSide": true,
            "pageLength": <?php print $PageLength; ?>,
            scrollX: "300px",
            scrollCollapse: true,
            processing: true,
            "language": {
                "processing": "<i class='fa fa-refresh fa-spin'></i>",
            },

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
        }); //End of dT.

        var table = $('#tabletemplates').DataTable();
        $('#tabletemplates tbody').on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });
        new $.fn.dataTable.Buttons(table, [{
                extend: 'copyHtml5',
                text: '<i class="fas fa-copy"></i>',
                titleAttr: 'Copy Selected',
                className: 'btn-primary',
                exportOptions: {
                    columns: [':visible']
                },
                enabled: false
            },
            {
                extend: 'excelHtml5',
                className: 'btn-primary',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                className: 'btn-primary',
                exportOptions: {
                    columns: [':visible']
                }
            },
            {
                text: '<i class="fas fa-refresh">Refresh</i>',
                titleAttr: 'Refresh',
                action: function(e, dt, node, config) {
                    refreshtemplate();
                },
                enabled: true
            }
        ]);
        table.buttons().container()
            .appendTo($('.col-md-6:eq(0)', table.table().container()));
        table.on('select deselect', function() {
            table.buttons(['.showonSelect']).enable(
                table.rows({
                    selected: true
                }).indexes().length === 0 ?
                false :
                true
            );
        }) //end of DT



        function refreshtemplate() {
             var table = $('#tabletemplates').DataTable();
            $.ajax({
                    url: "/templates/refreshtemplates",
                    method: "GET"
                })
                .done(function(data) {
                    var obj = JSON.parse(data); // Parse data, not msg
                    var status = obj.status;
                    var msg = obj.msg;
                    if (status == "success") {
                        toastr.success(msg);
                        
                        table.ajax.reload();
                    } else {
                        toastr.warning(msg);
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    // Handle AJAX error here
                    console.error("AJAX Error:", textStatus, errorThrown);
                    toastr.error("An error occurred during the AJAX request.");

                    // Hide DataTables loading indicator
                    $('#tabletemplates').DataTable().processing(false);
                   // $("body").removeClass("loading");
                });
        }






    });
    <?php $this->Html->scriptEnd(); ?>

