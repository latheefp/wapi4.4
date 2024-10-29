<?php
$this->Breadcrumbs->add([
    [
        'title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']
    ],
    [
        'title' => 'New User', 'url' => ['controller' => 'Settings', 'action' => 'newuser'],
        'options' => ['class' => 'breadcrumb-item active'],
    ]
]);
?>

<?php
echo $this->Form->create($user,
        [
            'type' => 'post',
            'class' => 'form-horizontal',
            'idPrefix' => 'newproduct_form',
            'id' => 'newuser_form',
            'defaction' => $action,
            'class' => ["form-horizontal", "needs-validation"],
            "novalidate",
        ]
);
?>               <div class="row" data-select2-id="15">
    <div class="form-group col-xl-6">
        <label>Name *</label>
        <input type="text" class="form-control" name="name" id="name" required="" placeholder="Name">
    </div>


    <div class="form-group col-xl-3">
        <label>Email Id </label>
        <input type="email" class="form-control" name="email" id="email" placeholder="Email Id">
    </div>
    <div class="form-group col-xl-3">
        <label>Mobile No. *</label>
        <input type="text" class="form-control" name="mobile_number" id="mobile_number" required="" placeholder="Mobile No.">
    </div>
    <?php
    $group_id = null;
    if (isset($data['ugroup_id'])) {
        $group_id = $data['ugroup_id'];
    }

    $session = $this->request->getSession();
    $admin_id = $session->read('Auth.User.ugroup_id');
   // debug($admin_id);
    //if AdminID=1, Superuser can be enabled. else disabled
    if ($admin_id == 1) {
          $filter_array = [];
    } else {
      
        $filter_array = array('NOT' => array('id' => 1));
    }
    ?>

    <div class="form-group col-xl-3">
        <label>Group </label>
        <select class="form-control select2bs4 "  maxlength="12"  minlength="12"  required="" name="ugroup_id" id="ugroup_id"  tabindex="-1" aria-hidden="true">
            <?php
            echo $this->Selectlist->buildlist([
                'table' => 'Ugroups',
                'selected' => $group_id,
                'where' => $filter_array,
                'field' => 'groupname',
                'placeholder', "Select Group"
            ]);
            ?>
        </select>
    </div>

    <div class="form-group col-xl-3">
        <label>Notes</label>
        <div>
            <textarea placeholder="Remarks" name="remarks" class="form-control" rows="2"></textarea>
        </div>
    </div>

    <div class="form-group col-xl-3" data-select2-id="37">
        <label class="control-label">Status</label>
        <select class="form-control" required="" name="active" data-select2-id="10" tabindex="-1" aria-hidden="true">
            <option value="1" data-select2-id="12">Enabled</option>
            <option value="0" data-select2-id="38">Disabled</option>
        </select>
    </div>
</div>
<hr>
<div class="row">
    <div class="form-group col-xl-3">
        <label>Login *</label>
        <input type="text" class="form-control" name="username" id="username" required="" autocomplete="off" placeholder="Username">
    </div>
    <div class="form-group col-xl-3">
        <label>Password *</label>
        <input type="password" class="form-control" name="password" id="password" required placeholder="Password">
    </div>
    <div class="form-group col-xl-3">
        <label>Password Confirm *</label>
        <input type="password" class="form-control" name="password1" id="password1" required  placeholder="Password Confirm">
    </div>
</div>
<hr chart="" of="" accounts="">
<div class="form-group mb-0">
    <div align="right">
        <button type="button" name="submit" id="newuser_add_form_button" class="btn btn-primary waves-effect waves-light mr-1">
            Submit
        </button>
        <button type="reset" class="btn btn-secondary waves-effect" onclick="goback();">
            Cancel
        </button>
    </div>
</div>
<?php echo $this->Form->end() ?>


<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
    $(function () {
        $("#newuser_add_form_button").click(function (event) {
            $('#newuser_form input, #newuser_form select').each(function (key, value) {
                this.classList.remove('is-invalid');
                this.setCustomValidity("");
            });
            var form = $("#newuser_form");
//  alert("Button clicked");
            if (form[0].checkValidity() === false)
            {
//          alert ("Validaty is false");
                ajaxvalidate(event);
            } else {
//         alert ("Validaty is true");
                ajaxvalidate(event);
            }
        });
    });

    function ajaxvalidate(event) {
        var form = $("#newuser_form")
        $.ajax({
            beforeSend: function (xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            url: "/ajaxes/validate/Users/add",
            method: "POST",
//  async:false,
            data: form.serialize(),
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
                        console.log(inputID + " Has error");
                        var input = document.getElementById(inputID);
                        input.classList.add('is-invalid');
                        input.setCustomValidity(msg);
//input.reportValidity();
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





    function submitregister() {
        var form = $("#newuser_form")
        var url = $('#newuser_form').attr('defaction');
        console.log(url);
        $.ajax({
            beforeSend: function (xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function (data)
            {
                var obj = JSON.parse(data);
                var status = obj.status;
                var msg = obj.msg;
                if (status == "success") {
                    toastr.success(msg);
                    setTimeout(function () {
                        // Redirect to the desired URL
                        window.location.href = "/settings/listusers";
                    }, 1000);

                } else {
                    toastr.error(msg);
                }
            }




        });
    }
    function goback() {
        location.href = "/settings/listusers";
    }


    <?php $this->Html->scriptEnd(); ?>

