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
            <?php
            foreach ($feildsType as $key => $val) {
                if ($val['viewable'] == true) {
                    //                             print '<th>'.$key.'</th>';
                    print '<th>' . $val['title'] . '</th>';
                }
            }
            ?>
            <th>Action</th>
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


        var table = $('#tableinvoice').DataTable({
            "ajax": {
                "url": "/invoices/getdata",
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
            //            select: true,
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
                ?> {
                    "data": null,
                    "sortable": false,
                    "searchable": false,
                    "orderable": false,
                    "targets": 0,
                    "render": function(data, type, full, row, index) {
                         console.log(data);
                        return '<div class="btn-group">' +

                           ' <button type="button" name="pay" title="Pay" id="' + data.DT_RowId + '" cname="' + data.Name + '" class="btn btn-outline-primary mr-1 btn-sm" ' + (data.Status === 'Unpaid' ? '' : 'disabled') + ' aria-label="Pay ' + data.Name + '"><i class="fas fa-pencil-alt"></i> Pay</button>'+
                            '<button type="button" name="details" title="View Bill" id="' + data.DT_RowId + '" cname="' + data.Name + '" class="btn btn-outline-info btn-sm" aria-label="View Bill for ' + data.Name + '"><i class="fas fa-paperclip"></i> View Bill</button>' +
                            '<button type="button" name="download" title="Download" id="' + data.DT_RowId + '" cname="' + data.Name + '" class="btn btn-outline-info btn-sm" aria-label="Download Bill for ' + data.Name + '"><i class="far fa-file-pdf"></i> Download</button>' +
                            '</div>';
                    }
                },
            ],
        }); //End of dT.

        $('#tableinvoice tbody').on('click', 'button', function() {
            var rowid = this.id;
            var action = this.name;
            var cname = this.getAttribute("cname");
         // alert(this.name);
            switch (action) {
                case 'pay':
                    editcamp(rowid, "edit");
                    break;
                case 'details':
                    window.location.href = "/invoices/details/"+rowid;
                    break;
                case 'download':
                    window.location.href = "/invoices/download/"+rowid;
                    break;   
                
                default:
                    // default code block
            }
        });

        var table = $('#tableinvoice').DataTable();
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
                text: '<i class="far fa-plus-square"></i>',
                className: 'btn btn-default btn-sm',
                titleAttr: 'Add New Campaign',
                action: function(e, dt, node, config) {
                    addcamp();
                },
                enabled: true
            }
        ]);
        table.buttons().container()
            .appendTo($('.col-md-6:eq(0)', table.table().container()));




      

    


    }); //end of DR.






   




    <?php $this->Html->scriptEnd(); ?>