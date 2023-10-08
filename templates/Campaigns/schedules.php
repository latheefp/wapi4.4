<?php
$this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']],
    ['title' => 'Templates', 'url' => ['controller' => 'Templates', 'action' => 'index']]
]);
?>

<div class="row">
    <div class="col-md-12 col-xs-12">
        <table id="tableschedule" class="table table-striped table-bordered dt-responsive nowrap dtselect" style="width:100%">
            <thead>
                <tr>
                    <th></th>
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
        <div>
            <div class="col-md-3">

            </div>
            <div class="col-md-9">
            </div>

        </div>


        <?php $this->Html->scriptStart(['block' => true]); ?>
        //<script>

            $(function () {

                var table = $('#tableschedule').DataTable({
                    "ajax": {
                        "url": "/campaigns/getschedules",
                        "type": "POST",
                        beforeSend: function (xhr) { // Add this line
                            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                        },
                    },
                    //        lengthChange: false,        
                    "stateSave": true,
                    "lengthMenu": [[5, 10, 15, 25, 50, 100], [5, 10, 15, 25, 50, 100]],
                    "processing": true,
                    "serverSide": true,
                    "pageLength": <?php print $PageLength; ?>,
                    scrollX: "300px",
                    scrollCollapse: true,
                    select: false,
                    "columns": [
                        {
                            className: 'dt-control',
                            orderable: false,
                            data: null,
                            defaultContent: ''
                           
                        },
<?php
foreach ($feildsType as $key => $val) {
    if ($val['viewable'] == true) {
        if ($val['searchable'] == 1) {
            $searchable = "true";
        } else {
            $searchable = "false";
        }
        print '{"data":"' . $val['title'] . '", "name":"' . $val['fld_name'] . '","searchable":' . $searchable . '},' . "\n";
    }
}
?>


                    ],
                }
                ); //End of dT.

                var table = $('#tableschedule').DataTable();
                $('#tableschedule tbody').on('click', 'tr', function () {
                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                        table.button(1).disable();
                        table.button(2).disable();
                    } else {
                        table.$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                        table.button(1).enable();
                        table.button(2).enable();
                        //    loaddetails();

                    }
                });












                new $.fn.dataTable.Buttons(table, [

                    {
                        text: '<i class="far fa-plus-square"></i>',
                        className: 'btn btn-info btn-sm',
                        titleAttr: 'New Schedule',
                        action: function (e, dt, node, config) {
                            addsched();
                        },
                        enabled: true
                    },
                    {
                        text: '<i class="far fa-trash-alt"></i>',
                        className: 'btn btn-info btn-sm',
                        titleAttr: 'Edit Schedule',
                        action: function (e, dt, node, config) {
                            deletesched();
                        },
                        enabled: false
                    },
                    {
                        text: '<i class="fas fa-play"></i>',
                        className: 'btn btn-info btn-sm',
                        titleAttr: 'Test It',
                        action: function (e, dt, node, config) {
                            testsched();
                        },
                        enabled: false
                    }


                ]);
                table.buttons().container()
                        .appendTo($('.col-md-6:eq(0)', table.table().container()));




                $("#newsched-btn").click(function (event) {
                    $('#schedform input, #schedform select').each(function (key, value) {
                        this.classList.remove('is-invalid');
                        this.setCustomValidity("");
                    });
                    var form = $("#schedform");
                    //  alert("Button clicked");
                    if (form[0].checkValidity() === false) {
                        //          alert ("Validaty is false");
                        ajaxvalidate(event);
                    } else {
                        //         alert ("Validaty is true");
                        ajaxvalidate(event);
                    }
                });






                $('#tableschedule tbody').on('click', 'td.dt-control', function () {
                    var tr = $(this).closest('tr');
                    var row = table.row(tr);
                    // console.log(row);

                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                    } else {
                        row.child(format(row.data())).show();
                        tr.addClass('shown');
                    }
                });


                function format(rowData) {
                    var div = $('<div/>')
                            .addClass('loading')
                            .text('Loading...');
                    //   console.log(rowData);
                    $.ajax({
                        url: '/campaigns/getscheddetails/' + rowData.DT_RowId,
//                        data: {
//                            id: rowData.DT_RowId
//                        },
                        //  dataType: 'json',
                        success: function (json) {
                            //   console.log(json);
                            div
                                    .html(json)
                                    .removeClass('loading');
                        }
                    });

                    return div;
                }



            }) //end of DR.






            function  addsched() {
                $('#schedmodel').modal({backdrop: 'static', keyboard: false});
                $('#schedform').attr('defaction', '/campaigns/newsched');
                $('#schedform').attr('validatefunction', 'add');
            }

            function deletesched() {
                var r = confirm("Do you want to delete this API?");
                if (r == true) {
                    var table = $('#tableschedule').DataTable();
                    var id = table.row('.selected').id();
                    $.ajax({
                        beforeSend: function (xhr) { // Add this line
                            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                        },
                        url: "/campaigns/deletesched/" + id,
                        method: "GET",
                        success: function (data) {
                            var obj = JSON.parse(data);
                            var status = obj.status;
                            var msg = obj.msg;
                            if (status == "success") {
                                toastr.success(msg);
                                $('#schedmodel').modal('hide');
                                var table = $('#tableschedule').DataTable();
                                table.ajax.reload();
                            } else {
                                toastr.error(msg);
                            }
                        }
                    })
                }


            }


            function testsched() {
                var table = $('#tableschedule').DataTable();
                var id = table.row('.selected').id();
                $.ajax({
                    beforeSend: function (xhr) { // Add this line
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    },
                    url: "/campaigns/test/" + id,
                    method: "GET",
                    success: function (data) {
                        var obj = JSON.parse(data);
                        var status = obj.status;
                        var msg = obj.msg;
                        if (status == "success") {
                            toastr.success(msg);
                            $('#schedmodel').modal('hide');
                            var table = $('#tableschedule').DataTable();
                            table.ajax.reload();
                        } else {
                            toastr.error(msg);
                        }
                    }
                })

            }

            function ajaxvalidate(event) {
                var form = $("#schedform")
                data = form.serialize();
                console.log(data);
                var validatefunction = $('#schedform').attr('validatefunction');
                $.ajax({
                    beforeSend: function (xhr) { // Add this line
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    },
                    url: "/ajaxes/validate/Schedules/" + validatefunction,
                    method: "POST",
                    data: data,
                    //success: successCallBack
                })
                        .done(function (data) {
                            var jsonData = JSON.parse(data);
                            var validStatus = true;
                            for (var i = 0; i < jsonData.length; i++) {
                                var counter = jsonData[i];
                                var inputID = counter.field;
                                if (inputID.endsWith("_id")) {
                                    inputID = inputID.substring(0, inputID.length - 3);
                                }
                                var msg = counter.error;
                                console.log(inputID);
                                var input = document.getElementById(inputID);
                                input.classList.add('is-invalid');
                                input.setCustomValidity(msg);
                                input.reportValidity();
                                validStatus = false;
                                input.reportValidity();
                            }
                            if (validStatus == false) {
                                event.preventDefault()
                                event.stopPropagation()
                            } else {
                                submitform();
                            }
                        });
            }


            function submitform() {


                //  alert("Submitting the form");

                var form = $("#schedform")
                var url = $('#schedform').attr('defaction');
                $.ajax({
                    beforeSend: function (xhr) { // Add this line
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    },
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (data) {
                        var obj = JSON.parse(data);
                        var status = obj.status;
                        var msg = obj.msg;
                        if (status == "success") {
                            toastr.success(msg);
                            $('#schedmodel').modal('hide');
                            var table = $('#tableschedule').DataTable();
                            table.ajax.reload();
                        } else {
                            toastr.error(msg);
                        }
                    }




                });
            }



            //</script>
        <?php $this->Html->scriptEnd(); ?>



        <div class="modal fade bs-example-modal-lg table-responsive" id="schedmodel"  tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="cat-modal-title">Schedule Campaign</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!--<div class="card card-primary">-->


                        <?php
                        $products = null;
                        echo $this->Form->create($products,
                                [
                                    'type' => 'post',
                                    'class' => 'form-horizontal',
                                    'url' => '/contacts/newcontactlist',
                                    'idPrefix' => 'newcontactlist',
                                    'id' => 'schedform',
                                    'defaction' => null,
                                    'class' => ["form-horizontal", "needs-validation"],
                                    "novalidate",
                                    'enctype' => 'multipart/form-data'
                                ]
                        );
                        ?>
                        <div class="card-body">
                            <input type="hidden" id="" name="id">
                            <input type="hidden" id="contact_id" name="contact_id">
                            <div class="row ">
                                <div tabindex="1" class="form-group col-md-4">
                                    <label>Schedule Name *</label>
                                    <input type="text" class="form-control" name="name" id="name" required="" placeholder="Name">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Campaign Name </label>
                                    <select class="form-control select2bs4 "    maxlength="12"  minlength="12"  required="" name="campaign_id" id="compaign_id"  tabindex="-1" aria-hidden="true">
                                        <?php
                                        $selected = null;
                                        if (isset($data['campaign_id'])) {
                                            $selected = $data['campaign_id'];
                                        }
                                         $session = $this->request->getSession();
                                        echo $this->Selectlist->buildlist([
                                            'table' => 'CampaignViews',
                                            'selected' => $selected,
                                            'where'=>array('status'=>"APPROVED", 'account_id' => $session->read('Auth.User.account_id')),
                                            'field' => 'campaign_name',
                                            'placeholder', "placeholder"
                                        ])

                                                
                                                ?>
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Contact List </label>
                                    <select class="form-control select2bs4 "  multiple="multiple"  maxlength="12"  minlength="12"  required="" name="contact_id[]" id="contact_id"  tabindex="-1" aria-hidden="true">
                                        <?php
                                        $contact_id = null;
                                        if (isset($data['contact_id'])) {
                                            $selected = $data['contact_id'];
                                        }
                                        echo $this->Selectlist->buildlist([
                                            'table' => 'Contacts',
                                            'selected' => $selected,
                                            'field' => 'name',
                                            'placeholder', "placeholder"
                                        ])
                                        ?>
                                    </select>
                                </div>


                            </div>


                        </div>
                        <div class="modal-footer">
                            <div class="form-group mb-0">
                                <div align="right">
                                    <button type="button" name="submit" id="newsched-btn" class="btn btn-primary waves-effect waves-light mr-1">
                                        Send Now
                                    </button>
                                    <button type="button"  data-dismiss="modal" id="cancle-btn" class="btn btn-secondary waves-effect">
                                        Cancel
                                    </button>
                                </div>
                            </div>

                        </div>
                        <?php echo $this->Form->end() ?>
                        <!--</div>-->
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>  