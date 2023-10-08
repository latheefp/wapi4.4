<div class="row">
    <div class="card card-default col-sm-6 card card-primary card-outline direct-chat direct-chat-primary shadow-none">
        <div class="card-header">
            <h3 class="card-title">New Campaigns</h3>
        </div>

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
                    <div tabindex="1" class="form-group col-xl-12">
                        <label>Start Date *</label>
                        <input type="date" class="form-control" name="start_date" id="start_date" required="" placeholder="Start Date">
                    </div>

                </div>
                <div class="col-md-6 col-xl-6 col-sm-12">
                    <div class="form-group">
                        <label>Template </label>
                        <select class="form-control select2 "  maxlength="12"  minlength="12"  required="" name="template_id" id="template_id"  tabindex="-1" aria-hidden="true">
                            <?php
                            $template_id = null;
                            if (isset($data['template_id'])) {
                                $template_id = $data['template_id'];
                            }
                            echo $this->Selectlist->buildlist([
                                'table' => 'Templates',
                                'selected' => $template_id,
                                'field' => 'name',
                                'placeholder', "Select Template"
                            ]);
                            ?>
                        </select>
                    </div>
                    <div  tabindex="2" class="form-group">
                        <label>End Date *</label>
                        <div>
                            <input type="date" class="form-control" name="end_date" id="end_date" required="" placeholder="End Date">
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
    <div id="msgbox" class="card card-default col-md-6"> 

    </div>
</div>



<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
    $(function () {

        $(".content").on("change", ".custom-file-input", function () {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        $('#template_id').on('select2:selecting', function (e) {
            id = e.params.args.data.id;
            $.ajax({
                beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                type: "POST",
                url: '/templates/gettemplatejson/' + id,
                success: function (data) {
                    $('#msgbox').html(data);
                }
            });

            $.ajax({
                beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                type: "POST",
                url: '/templates/gettemplatevars/' + id,
                success: function (data) {
                    $('#variables').html(data);
                }
            });
        })
    })


    $('#newcamp-btn').click(function (event) {
        //  alert("you are submitting form.")
//            document.getElementById("submitdatabutton").hidden = true;
//            document.getElementById("submitspin").hidden = false;

        event.preventDefault();
        var form = $("#newcampform");
        var form2 = $('#varform');
        data = form.serialize();
        data2 = form2.serialize();

        formData = new FormData(),
                params = form.serializeArray(),
                params2 = form2.serializeArray(),
                file = form2.find('input[type="file"]')[0].files;
        ; //
        //var form = $("#newcampform");
        var ins = form2.find('input[type="file"]').length;
        for ($i = 0; $i < ins; $i++) {
            $fname = form2.find('input[type="file"]')[$i]['name'];
            formData.append('file[' + $fname + ']', form2.find('input[type="file"]')[$i].files[0]);
        }



        $.each(params, function (i, val) {
            formData.append('[campaign]' + val.name, val.value);
        });
        $.each(params2, function (i, val) {
            formData.append('[campaign]' + val.name, val.value);
        });
        // formData.append('id', Math.random());

        console.log(formData);
        $.ajax({
            url: "/campaigns/newcamp",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function (xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            }
        })
                .done(function (data) {
                    var jsonData = JSON.parse(data);
                    status = jsonData.status;
                    msg = jsonData.msg;
                    if (status == "success") {
                        toastr['success'](msg);
                        $('#groupaddbutton').show();
                        $('#contactaddinputdiv').hide();
                        loadcontactlist();
                    } else {
                        toastr['error'](msg);
                    }
                    document.getElementById("submitdatabutton").hidden = false;
                    document.getElementById("submitspin").hidden = true;
                });

    })

    //</script>
<?php $this->Html->scriptEnd(); ?>