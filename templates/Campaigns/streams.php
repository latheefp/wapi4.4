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
                                                echo $this->Selectlist->buildlist([
                                                    'table' => 'Templates',
                                                    'where' => array('active' => true),
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
                "url": "/campaigns/getstreams",
                "type": "POST",
                beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
            },
            "createdRow": function (row, data, dataIndex) {
                //   console.log(data.Type);
                if (data.Type == "receive") {
                    $(row).addClass('table-primary');

                }
                if (data.Type == "ISend") {
                    $(row).addClass('table-info');
                }

                //   console.log(data.commented);
                if (data.commented == true) {
                    $(row).addClass('table-success');
                }



            },
            //        lengthChange: false,        
            "stateSave": true,
            "lengthMenu": [[5, 10, 15, 25, 50, 100], [5, 10, 15, 25, 50, 100]],
            "processing": true,
            "serverSide": true,
            "pageLength": <?php print $PageLength; ?>,
            scrollX: "300px",
            order: [[0, 'desc']],
            scrollCollapse: true,
//            select: true,
            "columns": [
                {
                    className: 'dt-control',
                    searchable: false,
                    orderable: false,
                    sortable: false,
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
            print '{"data":"' . $val['title'] . '", "name":"' . $val['fld_name'] . '", "width":"' . $val['width'] . '%",' . '"searchable":' . $searchable . '},' . "\n";
        }
    }
    ?>

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

        $(document).on('click', '.resendmsg', function () {
            var table = $('#tablecampaign').DataTable();
            var tr = $(this).closest('tr')
            var rowid = table.row(tr).id();
            $.ajax({
                url: "/campaigns/resend/" + rowid,
                method: "GET"
            })
                    .done(function (data) {


                        var obj = JSON.parse(data);
                        var status = obj.status;
                        var msg = obj.msg;
                        if (status == "success") {
                            var table = $('#tablecampaign').DataTable();
                            table.ajax.reload('null', false);
                            toastr.success("Message has been resent");
                        } else {
                            toastr.error(msg);
                        }

                    });

        });



        $('#tablecampaign tbody').on('click', 'td.dt-control', function () {
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




    }); //end of DR.


    function format(rowData) {
        var div = $('<div/>')
                .addClass('loading')
                .text('Loading...');
        //   console.log(rowData);
        $.ajax({
            url: '/campaigns/getstreamdetails/' + rowData.DT_RowId,
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


    function sendUpdate(id) {
        var updateText = $("#updateText" + id).val();

        $.ajax({
            type: "POST",
            url: "/campaigns/updatecomment", // Replace with your backend script URL
            data: {
                stream_id: id,
                comment: updateText
            },
            beforeSend: function (xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function (response) {
                // Display toaster notification
                toastr.success("Update successful!");



                response.data.forEach(function (commentData) {
                    var commentHtml = `
                    <div class="comment">
                        <div class="comment-header">
                            <span class="username">${commentData.user.name}</span>
                            <span class="timestamp">Just Now</span>
                        </div>
                        <div class="comment-content">
                            ${commentData.comment}
                        </div>
                    </div>
                `;
                    $('.comment-section-'+commentData.stream_id).append(commentHtml);
                });



                // Clear comment input field
                $("#updateText" + id).val('');

                // Call a function to load and append the latest data
                //  loadAndAppendLatestData();
            },
            error: function (xhr, status, error) {
                // Display error toaster notification
                toastr.error("Update failed: " + error);
            }
        });
    }



    <?php $this->Html->scriptEnd(); ?>
    