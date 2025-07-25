<?php
//debug($feildsType);
$this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']],
    ['title' => 'Schedules', 'url' => ['controller' => 'Campaigns', 'action' => 'schedules']]
]);
?>
<table id="apitable" class="table table-striped table-bordered dt-responsive nowrap dtselect" style="width:100%">
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


        var table = $('#apitable').DataTable({
            "ajax": {
                "url": "/settings/getapis",
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

        var table = $('#apitable').DataTable();
        $('#apitable tbody').on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                table.button(1).disable();
                table.button(2).disable();
                 table.button(3).disable();
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                table.button(1).enable();
                settablebutton();
                table.button(2).enable();
                 table.button(3).enable();

            }
        });



        new $.fn.dataTable.Buttons(table, [

            {
                text: '<i class="far fa-plus-square"></i>',
                className: 'btn btn-default btn-sm',
                titleAttr: 'Add',
                action: function (e, dt, node, config) {
                    addapi();
                },
                enabled: true
            },
            {
                text: '<i class="far fa-trash-alt"></i>',
                className: 'btn btn-default btn-sm',
                titleAttr: 'Delete',
                action: function (e, dt, node, config) {
                    deleteapi();
                },
                enabled: false
            },
            {
                text: '<i class="fas fa-toggle-on btn-toggle"></i>',
                className: 'btn btn-default btn-sm',
                titleAttr: 'Disable/Enabled',
                action: function (e, dt, node, config) {
                    togglestate();
                },
                enabled: false
            },
            {
                text: '<i class="fas fa-key btn-copy-key"></i>',
                className: 'btn btn-default btn-sm',
                titleAttr: 'Copy API Key',
                action: function (e, dt, node, config) {
                    var row = dt.row('.selected');
                    if (!row.node()) {
                        alert("Please select a row first.");
                        return;
                    }

                    var rowData = row.data();
                    var apiKey = rowData.full_api_key;

                   navigator.clipboard.writeText(apiKey).then(function () {
                        toastr.success("API key copied to clipboard!", "Success");
                    }, function (err) {
                        toastr.error("Failed to copy API key: " + err, "Error");
                    });
                },
                enabled: false // You can enable it dynamically when a row is selected
            }
        ]);
        table.buttons().container()
                .appendTo($('.col-md-6:eq(0)', table.table().container()));

    }) //end of DR





    function  addapi() {
        $('#apimodel').modal({backdrop: 'static', keyboard: false});
        $('#apiform').attr('defaction', '/settings/newapi');
        $('#apiform').attr('validatefunction', 'add');
    }

        function settablebutton() {
            var table = $('#apitable').DataTable();
            var row = table.row('.selected');

            if (!row.node()) {
                console.log("No row selected");
                return;
            }

            var rowIndex = row.id(); // optional
            var rowData = row.data();
            var isEnabled = rowData.enabled;

            if (isEnabled == 1 || isEnabled === true) {
                $('.btn-toggle').addClass("fa-toggle-on");
                $('.btn-toggle').removeClass("fa-toggle-off");
            } else {
                $('.btn-toggle').addClass("fa-toggle-off");
                $('.btn-toggle').removeClass("fa-toggle-on");
            }
        }


    function togglestate() {
        var table = $('#apitable').DataTable();
        rowIndex = table.row('.selected').id();
        var url = "/settings/toggleapistate/" + rowIndex;
        $.ajax({url: url}).done(function (status) {
            if (status == 1) {
                toastr.success("Enabled");

            } else {
                toastr.success("Disabled");
            }
            var table = $('#apitable').DataTable();
            table.ajax.reload();
            settablebutton();

        })


    }


    function deleteapi() {
        var table = $('#apitable').DataTable();
        rowIndex = table.row('.selected').id();
        var r = confirm("Do you want to delete this API?");
        if (r == true) {
            var url = "/settings/deleteapi/" + rowIndex;
            $.ajax({url: url}).done(function (msg) {
                var obj = JSON.parse(msg);
                var status = obj.status;
                var msg = obj.msg;
                if (status == "success") {
                    toastr.success(msg);
                    table.ajax.reload();
                } else {
                    toastr.error(msg);
                }
            });
        }

    }



    function ajaxvalidate(event) {
        var form = $("#apiform")
        data = form.serialize();
        console.log(data);
        var validatefunction = $('#apiform').attr('validatefunction');
        $.ajax({
            beforeSend: function (xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            url: "/ajaxes/validate/ApiKeys/" + validatefunction,
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

    $("#newapibtn").click(function (event) {
        $('#apiform input, #newproduct_form select').each(function (key, value) {
            this.classList.remove('is-invalid');
            this.setCustomValidity("");
        });
        var form = $("#apiform");
        //  alert("Button clicked");
        if (form[0].checkValidity() === false) {
            //          alert ("Validaty is false");
            ajaxvalidate(event);
        } else {
            //         alert ("Validaty is true");
            ajaxvalidate(event);
        }
    });



    function submitform() {


        //  alert("Submitting the form");

        var form = $("#apiform")
        var url = $('#apiform').attr('defaction');
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
                    $('#apimodel').modal('hide');
                    var table = $('#apitable').DataTable();
                    table.ajax.reload();
                } else {
                    toastr.error(msg);
                }
            }




        });
    }


    //</script>
<?php $this->Html->scriptEnd(); ?>


<div class="modal fade " id="apimodel"  tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="cat-modal-title">New API</h4>
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
                                            'id' => 'apiform',
                                            'defaction' => null,
                                            'class' => ["form-horizontal", "needs-validation"],
                                            "novalidate",
                                            'enctype' => 'multipart/form-data'
                                        ]
                                );
                                ?>
                                <div class="row ">

                                    <div tabindex="1" class="form-group col-xl-6 col-md-6">
                                        <label>API Name*</label>
                                        <input type="text" class="form-control" name="api_name" id="api_name" required="" placeholder="Uniq API Name">
                                    </div>
                                    <div tabindex="1" class="form-group col-xl-6 col-md-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Status</label>
                                            <select class="form-control" id="enabled"  name="enabled">
                                                <option value=1>Enabled</option>
                                                <option value=0>Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div tabindex="1" class="form-group col-xl-6 col-md-6">
                                        <label>White Listed IPs</label>
                                        <input type="text" class="form-control" name="ip_list" id="ip_list" required="" placeholder="IP1, IP2, IP3">
                                    </div>
                                </div>
                                <?php echo $this->Form->end() ?>

                                <div class="modal-footer">
                                    <div class="form-group mb-0">
                                        <div align="right">
                                            <button type="button" name="submit" id="newapibtn" class="btn btn-primary waves-effect waves-light mr-1">
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





