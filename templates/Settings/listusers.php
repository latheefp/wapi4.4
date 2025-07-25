<?php
$this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']],
    ['title' => 'Users', 'url' => ['controller' => 'Settings', 'action' => 'listusers']]
]);
?>

<table id="tblusers" class="table table-bordered table-striped dtselect" style="width:100%">
    <thead>
        <tr>
            <?php
          //  debug($feildsType);
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


<div class="modal fade" id="password-modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Password Change</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="passchangeform">
                    <input type="hidden" id="user_id" name="user_id">


                    <div class="form-group">
                        <label for="new_pass">New Password :</label>
                        <input class="form-control" type="password" name="password" id="password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_pass">Confirm Password :</label>
                        <input class="form-control" type="password" name="password1" id="password1">
                    </div>

                    <div class="modal-footer">
                        <!-- <input type="submit" name="submit" class="btn btn-block btn-warning" value="Save changes" /> -->
                        <button type="submit" name="submit" class="btn btn-success btn-sm" id="submitForm"
                                value="Save changes">Save Changes</button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<?php $this->Html->scriptStart(['block' => true]); ?>
//
//<script>
    var editor;
    $(function () {
        var table = $('#tblusers').DataTable({
            "ajax": {
                "url": "/settings/getuserdata",
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
            //scrollY:        "300px",
            scrollCollapse: true,
            select: true,
            "columns": [
    <?php
    foreach ($feildsType as $key => $val) {
      //  debug($val);
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

        var table = $('#tblusers').DataTable();

        $('#tblusers tbody').on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });




        // Display the buttons
        new $.fn.dataTable.Buttons(table, [
            {
                extend: 'copyHtml5',
                text: '<i class="fas fa-copy"></i>',
                titleAttr: 'Copy Selected',
                className: 'copy showonSelect',
                exportOptions: {
                    columns: [':visible']
                },
                enabled: false
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [':visible']
                }
            },
            {
                text: '<i class="fas fa-lock"></i>',
                titleAttr: 'Change password',
                className: 'pass showonSelect',
                action: function (e, dt, node, config) {
                    passchange();
                },
                enabled: false
            },
            {
                text: '<i class="fas fa-user-plus"></i>',
                titleAttr: 'Add New User',
                className: 'newuser',
                action: function (e, dt, node, config) {
                    newuser();
                },
                enabled: true
            },
            {
                text: '<i class="fas fa-lock"></i>',
                titleAttr: 'Change Pssword',
                className: 'edituser showonSelect',
                action: function (e, dt, node, config) {
                    passchange();
                },
                enabled: false
            },
            {
                text: '<i class="fas fa-user-edit"></i>',
                titleAttr: 'Edit User',
                className: 'edituser showonSelect',
                action: function (e, dt, node, config) {
                    edituser();
                },
                enabled: false
            },
            {
                text: '<i class="fas fa-trash"></i>',
                titleAttr: 'Delete User',
                className: 'edituser showonSelect',
                action: function (e, dt, node, config) {
                    deleteuser();
                },
                enabled: false
            },

            'colvis',
        ]);

        table.buttons().container()
                .appendTo($('.col-md-6:eq(0)', table.table().container()));


        table.on('select deselect', function () {
            table.buttons(['.showonSelect']).enable(
                    table.rows({selected: true}).indexes().length === 0 ?
                    false :
                    true
                    );
        })

        $("#passchangeform").on("submit", function (e) {
            $(".error").hide();
            var hasError = false;
            var password = $("#password").val();
            var password1 = $("#password1").val();
            console.log("validating " + password + " and  " + password1);
            if (password == '') {
                $("#password").after('<span class="error text-danger"><em>Please enter a password.<br></em></span>');
                hasError = true;
            } else if (password1 == '') {
                $("#password1").after('<span class="error text-danger"><em>Please re-enter your password.<br></em></span>');
                hasError = true;
            } else if (password != password1) {
                $("#password1").after('<span class="error text-danger"><em>Passwords do not match.<br></em></span>');
                hasError = true;
            }
            re = /[0-9]/;
            if (!re.test(password)) {

                $("#password").after('<span class="error text-danger"><em>password must contain at least one number (0-9)!<br></em></span>');
                passchangeform.password.focus();
                hasError = true;
            }

            re = /[a-z]/;
            if (!re.test(password)) {
                $("#password").after('<span class="error text-danger"><em>password must contain at least one lowercase letter (a-z)!<br></em></span>');
                passchangeform.password.focus();
                hasError = true;
            }

            re = /[A-Z]/;
            if (!re.test(password)) {
                $("#password").after('<span class="error text-danger"><em>password must contain at least one uppercase letter (A-Z)!<br></em></span>');
                passchangeform.password.focus();
                hasError = true;
            }

            if (hasError == true) {
                return false;
            }
            if (hasError == false) {
                console.log("Validation success, Posting")
                var postData = $(this).serializeArray();
                $.ajax({
                    url: '/users/setpassword',
                    type: "POST",
                    data: postData,
                    beforeSend: function (xhr) { // Add this line
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    },
                    success: function (data) {
                        var obj = JSON.parse(data);
                        var status = obj.status;
                        var msg = obj.msg;
                        if (status == "Success") {
                            toastr.success(msg);
                            $('#password-modal').modal('hide')
                        } else {
                            toastr.error(msg);
                        }
                    },
                    error: function (jqXHR, status, error) {
                        console.log(status + ": " + error);
                    }
                });
                e.preventDefault();
            }
        });

        $("#submitForm").on('click', function () {
            $("#updateForm").submit();
        });






    }); //End of default function.

    function passchange() {
        var table = $('#tblusers').DataTable();
        $('#user_id').val(table.row('.selected').id());
        $('#password-modal').modal({backdrop: 'static', keyboard: false});
    }


    function edituser() {
        var table = $('#tblusers').DataTable();
        var user_id = table.row('.selected').id();
        window.location.href = "/settings/edituser/" + user_id;

    }


    function deleteuser() {
        var result = confirm("Are you sure you want to delete the user?");
        if (result) {
            var table = $('#tblusers').DataTable();
            var user_id = table.row('.selected').id();
            $.ajax({
                url: '/settings/deletuser/' + user_id,
                type: "GET",

                success: function (data) {
                    var obj = JSON.parse(data);
                    var status = obj.status;
                    var msg = obj.msg;
                    if (status == "success") {
                        toastr.success(msg);
                        var table = $('#tblusers').DataTable();
                        table.ajax.reload();
                    } else {
                        toastr.error(msg);
                    }
                }
            })
        }

    }

    function newuser() {
        location.href = "/settings/newuser";
    }
    <?php $this->Html->scriptEnd(); ?>