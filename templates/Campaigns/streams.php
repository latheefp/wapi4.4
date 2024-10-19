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

<Input type="hidden" id="show-recieve-only" value="false">



<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
    $(function() {


        var table = $('#tablecampaign').DataTable({
            "ajax": {
                "url": "/campaigns/getstreams",
                "type": "POST",
                "data": function(d) {
                    d.show_recv = $('#show-recieve-only').val();
                },
                beforeSend: function(xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
            },
            "createdRow": function(row, data, dataIndex) {
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
            search: {
                    return: true  //enable enter to serach
                },
                language: {
                    searchPlaceholder: 'Press Enter to search'
                }   , 
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
            order: [
                [0, 'desc']
            ],
            scrollCollapse: true,
            //            select: true,
            "columns": [{
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


        new $.fn.dataTable.Buttons(table, [

            {
                text: '<i class="fas fa-eye-slash"></i>',
                className: 'btn btn-info btn-sm',
                titleAttr: 'Show only Receive',
                action: function(e, dt, node, config) {
                    togglercv();
                },
                enabled: true
            },
            {
                text: '<i class="far fa-share-square"></i>',
                className: 'btn btn-info btn-sm',
                titleAttr: 'Forward Me',
                action: function(e, dt, node, config) {
                    forwardme();
                },
                enabled: false
            },
            {
                text: '<i class="fas fa-share-square"></i>',
                className: 'btn btn-info btn-sm',
                titleAttr: 'Forward to',
                action: function(e, dt, node, config) {
                    forwardany();
                },
                enabled: false
            },
            {
                text: '<i class="fas fa-user-slash"></i>',
                className: 'btn btn-info btn-sm',
                titleAttr: 'Block this number',
                action: function(e, dt, node, config) {
                    blocknumber();
                },
                enabled: false
            },
            {
                text: '<i class="fas fa-paper-plane"></i>',
                className: 'btn btn-info btn-sm',
                titleAttr: 'Resend 24Hrs Failures',
                action: function(e, dt, node, config) {
                    sendfailed24h();
                },
                enabled: true
            }


        ]);
        table.buttons().container()
            .appendTo($('.col-md-6:eq(0)', table.table().container()));


      //  $('#tablecampaign_filter').append('<input id="customSearchInput"> <button id="customSearchBtn" class="btn btn-info btn-sm">Go</button>');




        // document.getElementById("customSearchInput").addEventListener("keydown", function(event) {
        //     // Check if the pressed key is not the Enter key
        //     if (event.key !== "Enter") {
        //         // Prevent default behavior for all keys except Enter
        //         event.preventDefault();
        //     }
        // });

        // // JavaScript to handle search action when Enter key is pressed
        // document.getElementById("customSearchInput").addEventListener("keyup", function(event) {
        //     // Check if the pressed key is the Enter key
        //     if (event.key === "Enter") {
        //         // Trigger the search action
        //         triggerSearch();
        //     }
        // });

        // // JavaScript to handle search action when the "Go" button is clicked
        // document.getElementById("customSearchBtn").addEventListener("click", function() {
        //     // Trigger the search action
        //     triggerSearch();
        // });

        // // Function to trigger search action
        // function triggerSearch() {
        //     var searchTerm = document.getElementById("customSearchInput").value;
        //     table.search(searchTerm).draw();
        // }






        // var table = $('#tableschedule').DataTable();
        $('#tablecampaign tbody').on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                table.button(1).disable();
                table.button(2).disable();
                table.button(3).disable();
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                table.button(1).enable();
                table.button(2).enable();
                table.button(3).enable();
                //    loaddetails();

            }
        });






        var table = $('#tablecampaign').DataTable();
        $('#tabletemplates tbody').on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });

        $(document).on('click', '.resendmsg', function() {
            var table = $('#tablecampaign').DataTable();
            var tr = $(this).closest('tr')
            var rowid = table.row(tr).id();
            $.ajax({
                    url: "/campaigns/resend/" + rowid,
                    method: "GET"
                })
                .done(function(data) {


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



        $('#tablecampaign tbody').on('click', 'td.dt-control', function() {
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



//disable auto filter. 
        // $("div.tablecampaign_filter input").unbind();

        // $("div.tablecampaign_filter input").on('keydown', function(e) {
        //     if (e.which == 13) {
        //         alert("Enter");
        //         table.search( $("div.tablecampaign_filter input").val()).draw();
        //     }
        // });





    }); //end of DR.


    // function togglercv() {


    // }

    function togglercv() {
        var table = $('#tablecampaign').DataTable();
        const showRecieveOnlyInput = document.getElementById("show-recieve-only");
        let newValue;

        // Get the current value (considering it might be a string representation of true/false)
        const currentValue = showRecieveOnlyInput.value;
        console.log(currentValue);
        if (currentValue === "true") {
            newValue = "false";
          //  table.ajax.reload();
        } else if (currentValue === "false") {
            newValue = "true";
          //  table.ajax.reload();
        } else {
            // Handle unexpected values (set to true by default)
            console.warn("Unexpected value in show-recieve-only input. Setting to true.");
            newValue = "true";
        }
        showRecieveOnlyInput.value = newValue;

        // Set the new value
        table.ajax.reload();
        
    }



    function forwardme() {
        var table = $('#tablecampaign').DataTable();
        var id = table.row('.selected').id();

        $.ajax({
            beforeSend: function(xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            url: "/campaigns/forwarderq/" + id,
            method: "GET",
            success: function(data) {
                var obj = JSON.parse(data);
                var status = obj.status;
                var msg = obj.msg;
                if (status == "success") {
                    toastr.success(msg);

                } else {
                    toastr.error(msg);
                }
            }
        })
    }

    function blocknumber() {
            var table = $('#tablecampaign').DataTable();
            var id = table.row('.selected').id();

            $.ajax({
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);  // Attach CSRF token for security
                },
                url: "/contacts/blocknumber/" + id,  // Make the AJAX call to block the number
                method: "GET",
                success: function(data) {
                    if (data.status === 'success') {
                        // Display success message using Toastr
                        toastr.success(data.message);
                    } else if (data.status === 'error') {
                        // Display error message using Toastr
                        toastr.error(data.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle any errors that might occur during the AJAX request itself
                    toastr.error('An error occurred while processing the request.');
                }
            });
        }


    function forward24h() {
        
    }


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
            success: function(json) {
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
            beforeSend: function(xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                // Display toaster notification
                toastr.success("Update successful!");



                response.data.forEach(function(commentData) {
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
                    $('.comment-section-' + commentData.stream_id).append(commentHtml);
                });



                // Clear comment input field
                $("#updateText" + id).val('');

                // Call a function to load and append the latest data
                //  loadAndAppendLatestData();
            },
            error: function(xhr, status, error) {
                // Display error toaster notification
                toastr.error("Update failed: " + error);
            }
        });
    }

    //
</script>

<?php $this->Html->scriptEnd(); ?>