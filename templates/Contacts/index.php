<div class="row">
    <div class="col-md-3">

        <div class="dn" id="addnewgroupdivWrap" style="display: block;">
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title load_numbers">Contacts Groups</h3>

            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="contact_table" class="table table-bordered mb-0 " style="width: 100%" ;>
                        <colgroup>
                            <col style="width: 75%;">
                            <col style="width: 25%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Count</th>
                            </tr>
                        </thead>


                    </table>
                </div>

            </div>

        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Labels</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-circle text-danger"></i>
                            Important
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-circle text-warning"></i> Promotions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-circle text-primary"></i>
                            Social
                        </a>
                    </li>
                </ul>
            </div>

        </div>

    </div>

    <div class="col-md-9">
        <div class="card card-primary card-outline">

            <div class="card-body p-0">
                <div class="mailbox-controls">

                    <div class="table-responsive mailbox-messages">
                        <table id="numberlist" class="table table-striped table-bordered dt-responsive nowrap dtselect" style="width:100%">
                            <thead>
                                <tr>
                                    <?php
                                    foreach ($feildsType as $key => $val) {
                                        if ($val['viewable'] == true) {
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

                    </div>

                </div>
            </div>

        </div>

    </div>
</div>



<div class="modal fade bs-example-modal-lg" id="contactmodel" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">Add Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Upload</a>
                        </li>

                    </ul>
                </div>

                <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                $products = null;
                                echo $this->Form->create(
                                    $products,
                                    [
                                        'type' => 'post',
                                        'class' => 'form-horizontal',
                                        'url' => '/contacts/newcontactlist',
                                        'idPrefix' => 'newcontactlist',
                                        'id' => 'newcontactlist',
                                        'defaction' => null,
                                        'class' => ["form-horizontal", "needs-validation"],
                                        "novalidate",
                                        'enctype' => 'multipart/form-data'
                                    ]
                                );
                                ?>
                                <input type="hidden" id="" name="id">
                                <input type="hidden" id="contact_id" name="contact_id">
                                <div class="row ">
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div tabindex="1" class="form-group col-xl-12">
                                            <label>Name *</label>
                                            <input type="text" class="form-control" name="name" id="name" required="" placeholder="Name">
                                        </div>
                                        <div tabindex="1" class="form-group col-xl-12">
                                            <label>Mobile Number *</label>
                                            <input type="text" class="form-control" name="mobile_number" id="mobile_number" required="" placeholder="Mobile Number">
                                        </div>
                                        <div class="form-group">
                                            <label>Groups </label>
                                            <?php echo $this->element('Select2Ajaxelement', array('token' => '88975478KLU96C32', 'name' => "contact_id[]", 'id' => "group_ids")); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-6 col-sm-12">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select class="form-control" name="gender" , id="gender">
                                                    <option value="M">Male</option>
                                                    <option value="F">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div tabindex="2" class="form-group">
                                            <label>Expiry Date *</label>
                                            <div>
                                                <input type="date" class="form-control" name="expiry" id="expiry" required="" placeholder="Expiry date">
                                            </div>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox">
                                            <label class="form-check-label"> Blocked</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="form-group mb-0">
                                        <div align="right">
                                            <button type="button" name="submit" id="newcontact-btn" class="btn btn-primary waves-effect waves-light mr-1">
                                                Submit
                                            </button>
                                            <button type="button" data-dismiss="modal" id="cancle-btn" class="btn btn-secondary waves-effect">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                    <?php echo $this->Form->end() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                        <div class="card">
                            <div class="card-body">
                                <div id="fileupload_form" class="col-md-12">
                                    <form class="form-inline" id="newcontactupload" enctype="multipart/form-data">
                                        <div class="form-inline">
                                            <input type="file" name="contactfile" id="contactfile" class="form-control" placeholder="Upload list">
                                            <input type="hidden" name="filename" value="testval">
                                            <button type="button" id="fileupload-btn" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                                <br>
                                <div id="contendsubmitform" class="col-md-12" style="overflow-y: scroll; height:400px;">
                                    <form class="" id="submitselecteddata_form" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Groups </label>
                                            <?php echo $this->element('Select2Ajaxelement', array('token' => '88975478KLU96C32', 'name' => "contact_id[]", 'id' => "group_ids2")); ?>
                                        </div>
                                        <div id="listtablecontent" class="col-md-12">
                                        </div>
                                        <br>
                                        <div class="form-inline mt-2">
                                            <button type="button" id="submitdatabutton" class="btn btn-primary">Submit</button>
                                            <button type="button" hidden disabled id="submitspin" class="btn btn-primary">
                                                <div class="spinner-border spinner-border-sm" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </button>
                                            <button type="button" data-dismiss="modal" id="cancle-btn" class="btn btn-secondary waves-effect">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
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



<input type="hidden" id="current_contact_id">
<input type="hidden" id="action"> 

<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
    $(function() {



        var contact_group_table = $('#contact_table').DataTable({
            "ajax": {
                "url": "/contacts/getlist",
                "type": "POST",
                "data": function(d) {
                    d.show_recv = $('#show-recieve-only').val();
                },
                beforeSend: function(xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
            },
            "stateSave": true,
            "processing": true,
            "serverSide": true,
            "paging": true,
            "select": true,
            "bInfo": false,

            "pageLength": 20,
            "lengthChange": false, // Disable the length menu completely
            //   "dom": "lfrtip", // Display all elements except length dropdown
            scrollX: "300px",
            order: [
                [0, 'desc']
            ],
            scrollCollapse: true,
            "columns": [

                {
                    data: "Name",
                    name: "name",
                    searchable: true

                },
                {
                    data: "Count",
                    name: "count",
                    searchable: false

                }

            ],

        }); //End of dT.

        new $.fn.dataTable.Buttons(contact_group_table, [

            {

                text: '<i class="fa fa-trash"></i>',
                className: 'btn btn-danger btn-sm',
                action: function(e, dt, node, config) {
                    var rowData = contact_group_table.row('.selected').data();
                    if (rowData) {
                        var name = rowData.Name; // Assuming 'Name' is the key for the name field, replace it with the actual key
                    }
                    id = contact_group_table.row('.selected').id();
                    deletecontact(id, name);
                   
                },
                enable: false
            },

            {
                text: '<i class="far fa-plus-square"></i>',
                className: 'btn btn-default btn-sm',
                titleAttr: 'Add New Contact',
                action: function(e, dt, node, config) {
                    addcontact();
                },
                enabled: true
            }
        ]);

        contact_group_table.buttons().container()
            .appendTo($('.col-md-6:eq(0)', contact_group_table.table().container()));


        contact_group_table.on('select deselect', function() {
            var selectedRows = contact_group_table.rows({
                selected: true
            }).count();
            var button0 = contact_group_table.button(0); // Assuming the first button

            if (selectedRows > 0) {
                button0.enable(); // Enable the button
            } else {
                button0.disable(); // Disable the button
            }

            var id = contact_group_table.row('.selected').id();
            $('#current_contact_id').val(id);
            $('#numberlist').DataTable().draw();
        });



        $('.custom-file input').change(function(e) {
            if (e.target.files.length) {
                $(this).next('.custom-file-label').html(e.target.files[0].name);
            }
        });

        $('#contendsubmitform').hide();

        $('#submitdatabutton').click(function(event) {
            //  alert("you are submitting form.")
            document.getElementById("submitdatabutton").hidden = true;
            document.getElementById("submitspin").hidden = false;

            event.preventDefault();
            var form = $("#submitselecteddata_form");
            data = form.serialize();
            console.log(data);
            $.ajax({
                    url: "/contacts/imporfromexcel",
                    method: "POST",
                    data: data,
                    beforeSend: function(xhr) { // Add this line
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    }
                })
                .done(function(data) {
                    var jsonData = JSON.parse(data);
                    status = jsonData.status;
                    msg = jsonData.msg;
                    if (status == "success") {
                        toastr['success'](msg);
                        $('#groupaddbutton').show();
                        $('#contactaddinputdiv').hide();
                        //   loadcontactlist();
                    } else {
                        toastr['error'](msg);
                    }
                    document.getElementById("submitdatabutton").hidden = false;
                    document.getElementById("submitspin").hidden = true;


                });


        })

        $('#fileupload-btn').click(function(event) {
            event.preventDefault();

            var form = $("#newcontactupload");
            formData = new FormData(),
                params = form.serializeArray(),
                file = form.find('[name="contactfile"]')[0].files;
            $.each(file, function(i, file) {
                //  console.log(file);
                formData.append('file[]', file);
            });
            $.each(params, function(i, val) {
                formData.append(val.name, val.value);
            });
            formData.append('id', Math.random());

            $.ajax({
                    beforeSend: function(xhr) { // Add this line
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    },
                    url: '/contacts/newcontactupload',
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    enctype: 'multipart/form-data',
                    processData: false,
                    xhr: function() {
                        myXhr = $.ajaxSettings.xhr();
                        console.log('XHR: ', myXhr);
                        if (myXhr.upload) {
                            myXhr.upload.addEventListener('progress', function(e) {
                                var loaded = e.loaded;
                                var total = e.total;
                                var percentComplete = Math.round((e.loaded / e.total) * 100);

                                // Quantity of bytes per second
                                var leftover_bytes = total - loaded;
                                var leftover_bytes = leftover_bytes / 1048576;
                                $('p#response').html(leftover_bytes.toFixed(2) + ' MB');

                                // Progress bar function
                                $('.progress .progress-bar').css('width', percentComplete + '%');
                                $('.progress .progress-bar').html(percentComplete + '%');

                            }, false);
                            myXhr.upload.addEventListener('load', function(e) {
                                // After upload cant reset progrees bar
                                //$('.progress .progress-bar').css('width', '0%');
                                //$('.progress .progress-bar').html( '');
                            }, false);
                        }
                        return myXhr;
                    }
                })
                .done(function(data, textStatus, jqXHR) { // Before success
                    //  alert("before success");
                    // $('#listtablecontent').html(data);
                })
                .fail(function(jqXHR, textStatus, errorThrown) { // Before error
                    //  alert(jqXHR.status);

                    switch (jqXHR.status) {
                        case 0:
                            $('p#response').html('Timeout exceeded.');
                            break;

                        case 403:
                            $('p#response').html('The session has expired.');
                            break;

                        case 404:
                            $('p#response').html('The requested page does not exist.');
                            break;

                        default:
                            if (jqXHR.status != 200) {
                                $('p#response').html('Error please try again has occurred.');
                            }
                            break;
                    }


                    console.log(jqXHR);

                }).done(function(logs) {

                    $('#fileupload_form').hide();
                    $('#listtablecontent').html(logs)
                    $('#contendsubmitform').show();


                });

            return false;

        });


        $('#contactaddinputdiv').hide();
        //  loadcontactlist();
        // $(".load_numbers").click(function(event) {
        //     type = event.target.getAttribute('type');
        //     if (type == "delete") {
        //         gname = event.target.getAttribute('gname');
        //         deletecontact(event.target.id, gname)
        //     } else {
        //         getcontacts(event.target.id);
        //         const selected = document.querySelectorAll('.navselected');
        //         selected.forEach(select => {
        //             select.classList.remove('navselected');
        //         });
        //         event.target.classList.add('navselected');
        //     }

        // });





        var table = $('#numberlist').DataTable({
            "ajax": {
                "url": "/contacts/getcontacts",
                "type": "POST",
                //                "type": "GET",
                "data": function(d) {
                    d.contact_id = $('#current_contact_id').val();
                },
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

        var table = $('#numberlist').DataTable();
        $('#tabletemplates tbody').on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });
        new $.fn.dataTable.Buttons(table, [

            {

                text: '<i class="far fa-file-excel"></i>',
                className: 'btn btn-primary btn-sm',
                exportOptions: {
                    columns: ':visible'
                }
            },

            {
                text: '<i class="far fa-plus-square"></i>',
                className: 'btn btn-default btn-sm',
                titleAttr: 'Add New Contact',
                action: function(e, dt, node, config) {
                    addcontact();
                },
                enabled: true
            },
            {
                text: '<i class="fa fa-ban"></i>',
                className: 'btn btn-default btn-sm',
                titleAttr: 'Block',
                action: function(e, dt, node, config) {
                    block();
                },
                enabled: true
            },
            {
                text: '<i class="fa fa-trash"></i>',
                className: 'btn btn-default btn-sm',
                titleAttr: 'Delete',
                action: function(e, dt, node, config) {
                    deletecontactnumber();
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
        })



        $("#newcontact-btn").click(function(event) {
            $('#newcontactlist input, #newproduct_form select').each(function(key, value) {
                this.classList.remove('is-invalid');
                this.setCustomValidity("");
            });
            var form = $("#newcontactlist");
            //  alert("Button clicked");
            if (form[0].checkValidity() === false) {
                //          alert ("Validaty is false");
                ajaxvalidate(event);
            } else {
                //         alert ("Validaty is true");
                ajaxvalidate(event);
            }
        });





    }); //end of DR




    function deletecontact(rowid, gname) {
        var r = confirm("Do you want to delete Contact Group" + gname + " " + rowid + " ?");
        if (r == true) {
            var url = "/contacts/deletecontact/" + rowid;
            $.ajax({
                url: url
            }).done(function(msg) {
                var obj = JSON.parse(msg);
                var status = obj.status;
                var msg = obj.msg;
                if (status == "success") {
                    toastr.success(msg);
                    //   loadcontactlist();
                } else {
                    toastr.error(msg);
                }
            });
        }

    }

    function addcontact() {
        $('#contactmodel').modal({
            backdrop: 'static',
            keyboard: false
        });
        //  $('#newccount_form').get(0).reset(); 
        $('#newcontactlist').attr('defaction', '/contacts/newcontactnumber');
        $('#action').val("add");
        $('#fileupload_form').show();
        $('#contendsubmitform').hide();
        document.getElementById("submitdatabutton").hidden = false;
        document.getElementById("submitspin").hidden = true;
    }


    function newgroup() {
        console.log("opening form for group add");
        $('#groupaddbutton').hide();
        $('#contactaddinputdiv').show();
    }

    function addnewgroup() {
        group = $('#newgrouptxt').val();
        $.ajax({
                url: "/contacts/newcontacts",
                method: "POST",
                data: {
                    'name': group
                },
                beforeSend: function(xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                }
            })
            .done(function(data) {
                var jsonData = JSON.parse(data);
                status = jsonData.status;
                msg = jsonData.msg;
                if (status == "success") {
                    toastr['success'](msg);
                    $('#groupaddbutton').show();
                    $('#contactaddinputdiv').hide();
                    //      loadcontactlist();
                } else {
                    toastr['error'](msg);
                }

            });
    }

    function cancelgroup() {
        $('#groupaddbutton').show();
        $('#contactaddinputdiv').hide();
    }

    // function loadcontactlist() {
    //     $.ajax({
    //         url: "/contacts/getmygroups/",
    //         method: "GET",
    //         //   data:{customer:query},  
    //         success: function (data)
    //         {
    //             $('#grouplist').html(data);
    //         }
    //     });
    // }

    // function getcontacts(id) {
    //     $('#current_contact_id').val(id);
    //     //   console.log("Redrawing table " + id)
    //     $('#numberlist').DataTable().draw();
    // }


    function ajaxvalidate(event) {
        var form = $("#newcontactlist")
        data = form.serialize();
        console.log(data);
        $.ajax({
                beforeSend: function(xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                url: "/ajaxes/validate/ContactNumbers/" + $('#action').val(),
                method: "POST",
                //  async:false,
                data: data,

                //success: successCallBack
            })
            .done(function(data) {
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

        var form = $("#newcontactlist")
        var url = $('#newcontactlist').attr('defaction');
        console.log(url);
        $.ajax({
            beforeSend: function(xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function(data) {
                var obj = JSON.parse(data);
                var status = obj.status;
                var msg = obj.msg;
                if (status == "Success") {
                    toastr.success(msg);
                    $('#numberlist').DataTable().draw();

                } else {
                    toastr.error(msg);
                }
            }




        });
    }







    <?php $this->Html->scriptEnd(); ?>