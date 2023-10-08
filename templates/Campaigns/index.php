<?php
//debug($feildsType);
$this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']],
    ['title' => 'Schedules', 'url' => ['controller' => 'Campaigns', 'action' => 'schedules']]
]);
?>
<table id="tablecampaign" class="table table-striped table-bordered dt-responsive nowrap dtselect" style="width:100%">
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


<div class="modal fade bs-example-modal-lg table-responsive" id="contactmodel"  tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="cat-modal-title">Contact Number</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-header p-0 pt-1 border-bottom-0">

                </div>

                <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                $products = null;
                                echo $this->Form->create($products,
                                        [
                                            'type' => 'post',
                                            'class' => 'form-horizontal',
                                            'url' => '/compaigns/newcamp',
                                            'idPrefix' => 'newcontactlist',
                                            'id' => 'newcampform',
                                            'defaction' => null,
                                            'class' => ["form-horizontal", "needs-validation"],
                                            "novalidate",
                                            'enctype' => 'multipart/form-data'
                                        ]
                                );
                                ?>
                                <div class="row ">
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div tabindex="1" class="form-group col-xl-12">
                                            <label>Campaign Name *</label>
                                            <input type="text" class="form-control" name="campaign_name" id="campaign_name" required="" placeholder="Campaign Name">
                                        </div>
                                        <div tabindex="1" class="form-group  datepicker col-xl-12  " >
                                            <label>Start Date *</label>
                                            <input type="text" class="form-control  " name="start_date" id="start_date" required="" placeholder="Start Date">
                                            <div class="input-group-addon">
                                                <span class="glyphicon glyphicon-th"></span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6 col-xl-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Template </label>
                                            <select class="form-control select2bs4 "  maxlength="12"  minlength="12"  required="" name="template_id" id="template_id"  tabindex="-1" aria-hidden="true">
                                                <?php
                                                $template_id = null;
                                                if (isset($data['template_id'])) {
                                                    $template_id = $data['template_id'];
                                                }
//                                                $session = $this->request->getSession();
                                              //  debug($session->read('Accunt.id'));
                                                echo $this->Selectlist->buildlist([
                                                    'table' => 'Templates',
                                                    'where' => array('account_id' =>$account_id),
                                                    'selected' => $template_id,
                                                    'field' => 'name',
                                                    'placeholder', "Select Template"
                                                ]);
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group  datepicker" >
                                            <label>End Date *</label>
                                            <input type="text" class="form-control  " name="end_date" id="end_date" required="" placeholder="End Date">
                                            <div class="input-group-addon ">
                                                <span class="glyphicon glyphicon-th"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php echo $this->Form->end() ?>
                                <form method="post" enctype="multipart/form-data" accept-charset="utf-8" class="form-horizontal needs-validation" id="varform">
                                    <div class="row">
                                        <div id="variables" class="col-md-12 col-sm-12" >


                                        </div>
                                    </div>
                                </form>
                                <div class="modal-footer">
                                    <div class="form-group mb-0">
                                        <div align="right">
                                            <button type="button" name="submit" id="newcamp-btn" class="btn btn-primary waves-effect waves-light mr-1">
                                                Submit
                                            </button>
                                            <button type="button"  data-dismiss="modal" id="cancle-btn" class="btn btn-secondary waves-effect">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>  

<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
    $(function () {


        var table = $('#tablecampaign').DataTable({
            "ajax": {
                "url": "/campaigns/getcampaign",
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
    ?>
                {
                    "data": null,
                    "sortable": false,
                    "searchable": false,
                    "orderable": false,
                    "targets": 0,
                    "render": function (data, type, full, row, index) {
                        // console.log(data);
                        return '<div class = "btn-group">' +
                                '<button type="button" name="edit"  title="Edit" id="' + data.DT_RowId + '" cname="' + data.Name + '" class="btn  btn-outline-primary mr-1 btn-sm"><i class="fas fa-pencil-alt"></i></button>' +
                                '<button type="button" name="delete" title="Delete"  id="' + data.DT_RowId + '"  cname="' + data.Name + '"  class="btn btn-outline-danger mr-1 btn-sm"><i class="fas fa-times-circle" data-toggle="tooltip" data-placement="top" title="Tooltip text"></i></button>' +
                                '<button type="button" name="schedule" title="Schedule"  id="' + data.DT_RowId + '"  cname="' + data.Name + '" class="btn btn-outline-info mr-1 btn-sm"><i class="fas fa-clock"></i></button>' +
                                '<button type="button" name="attachment" title="Attachments"  id="' + data.DT_RowId + '"  cname="' + data.Name + '" class="btn btn-outline-info btn-sm"><i class="fas fa-paperclip"></i></button>' +
                                '</div>'
                    }
                },
            ],
        }); //End of dT.

        var table = $('#tablecampaign').DataTable();
        $('#tabletemplates tbody').on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });
        new $.fn.dataTable.Buttons(table, [
            {
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
                action: function (e, dt, node, config) {
                    addcamp();
                },
                enabled: true
            }
        ]);
        table.buttons().container()
                .appendTo($('.col-md-6:eq(0)', table.table().container()));




        function refreshtemplate() {
            $.ajax({
                url: "/templates/refreshtemplates",
                method: "GET"
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
                            //    console.log(inputID + " Has error");
                            var input = document.getElementById(inputID);
                            input.classList.add('is-invalid');
                            input.setCustomValidity(msg);
                            validStatus = false;
                            input.reportValidity();
                        }
                        if (validStatus == false) {
                            event.preventDefault()
                            event.stopPropagation()
                        } else {
                            submitregister();
                        }
                    });
        }



        $("#newcamp-btn").click(function (event) {
            $('#newcampform input, #newproduct_form select').each(function (key, value) {
                this.classList.remove('is-invalid');
                this.setCustomValidity("");
            });
            var form = $("#newcampform");
            //  alert("Button clicked");
            if (form[0].checkValidity() === false) {
                //          alert ("Validaty is false");
                ajaxvalidate(event);
            } else {
                //         alert ("Validaty is true");
                ajaxvalidate(event);
            }
        });

        $('#tablecampaign tbody').on('click', 'button', function () {
            var rowid = this.id;
            var action = this.name;
            var cname = this.getAttribute("cname");
            //  alert(cname);
            switch (action) {
                case 'edit':
                    editcamp(rowid, "edit");
                    break;
                case 'delete':
                    deletecamp(rowid, cname);
                    break;
                case 'schedule':
                    window.location.href = '/campaigns/schedule/' + rowid;
                    break;
                case 'attachment':

                    window.location.href = '/campaigns/attachments/' + rowid;
                    break;
                default:
                // default code block
            }
        });
    }); //end of DR.


    function deletecamp(rowid, cname) {
        var r = confirm("Do you want to delete Campaign" + cname + "?");
        if (r == true) {
            $.ajax({url: '/campaigns/deletecamp/' + rowid}).done(function (msg) {
                var obj = JSON.parse(msg);
                var status = obj.status;
                var msg = obj.msg;
                if (status == "success") {
                    toastr.success(msg);
                    var table = $('#tablecampaign').DataTable();
                    table.ajax.reload();
                } else {
                    toastr.error(msg);
                }
            });
        }
//        });
    }

    function editcamp(rowid) {
        $('#contactmodel').modal({backdrop: 'static', keyboard: false});
        $('#newcampform').attr('defaction', '/campaigns/newcamp');
        $('#newcampform').attr('validatefunction', 'edit');
    }

    function  addcamp() {
        $('#contactmodel').modal({backdrop: 'static', keyboard: false});
        $('#newcampform').attr('defaction', '/campaigns/newcamp');
        $('#newcampform').attr('validatefunction', 'add');
    }



    function ajaxvalidate(event) {
        var form = $("#newcampform")
        data = form.serialize();
        console.log(data);
        var validatefunction = $('#newcampform').attr('validatefunction');
        $.ajax({
            beforeSend: function (xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            url: "/ajaxes/validate/Campaigns/" + validatefunction,
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

        var form = $("#newcampform")
        var url = $('#newcampform').attr('defaction');
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
                    $('#contactmodel').modal('hide');
                    var table = $('#tablecampaign').DataTable();
                    table.ajax.reload();
                } else {
                    toastr.error(msg);
                }
            }




        });
    }
    <?php $this->Html->scriptEnd(); ?>
    