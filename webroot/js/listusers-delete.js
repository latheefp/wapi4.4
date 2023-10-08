/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

  $(function () {
    editor = new $.fn.dataTable.Editor( {
        ajax: {
                    "url": "/users/usermgt",
                    beforeSend: function (xhr) { // Add this line
                   xhr.setRequestHeader('X-CSRF-Token', csrfToken );
               },
                }, 
        
        
        table: "#tblusers",
        fields: [ {
                label: "Username:",
                name: "username"
            }, {
                label: "Email:",
                name: "email"
            }, {
                label: "Phone:",
                name: "phone"
            },
           {
                label: "Active:",
                name: "active",
                type: "select",
                options: [
                    { label: "Active", value: "1" },
                    { label: "Inactive",value: "0" },
                
                ]
            },
             {
                label: "Groups:",
                name: "group_id",
                type:  "select2",
                attr: {
                multiple: "multiple"
                    },
                    optionsPair: {
//                        label: 'id',
//                        value: 'text'
                    }

                }
            
        ]
    } );    
      
      
      
   var table= $('#tblusers').DataTable({
      "ajax": {
                    "url": "/settings/getdata",
                }, 
//        lengthChange: false,        
        "stateSave": true,
        "lengthMenu": [[5, 10, 15, 25, 50, 100 ], [5, 10, 15, 25, 50, 100]],
        "processing": true,
        "serverSide": true,
        "pageLength": <?php  print $PageLength; ?>,
        scrollY:        "300px",
        scrollCollapse: true,
        select: true,
     //   dom:lf,
      //  dom:"<'row'<'col-sm-12 col-md-7 b'><'col-sm-12 col-md-5'lf>>" ,
        "columns": [
           <?php
            foreach($feildsType as $key =>$val){
                if($val['viewable']==true){
                    if($val['searchable']==1){$searchable="true";}else{$searchable="false";}
                    print '{"data":"' . $val['fld_name'].  '", "name":"'. $val['fld_name'].'", "width":"'.$val['width'].'%",'.'"searchable":'.$searchable.'},'."\n";
                }
            }
           ?>

        ],
        
        
        
        
        
        
        }); //End of dT.

    
   
    
   // Display the buttons
    new $.fn.dataTable.Buttons( table, [
            { extend: "create", editor: editor },
            { extend: "edit",   editor: editor },
            { extend: "remove", editor: editor },
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [ ':visible' ]
                }
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
                    columns: [ ':visible' ]
                }
            },
//            {
//                extend: 'csvHtml5',
//                exportOptions: {
//                    columns: [ ':visible' ]
//                }
//            },
            {
                text:"Pass",
                className: 'pass',
                action: function ( e, dt, node, config ) {
                                    passchange();
                                },
                enabled: false
            },
          
            'colvis',
    ] );

   table.buttons().container()
        .appendTo( $('.col-md-6:eq(0)', table.table().container() ) );
   
   
     table.on( 'select deselect', function () {
        table.buttons( ['.pass'] ).enable(
               table.rows( { selected: true } ).indexes().length === 0 ?
                   false :
                   true
           );
     })
   
   
   
   editor.on('initEdit', function (e, node, data, items, type) {
        $.getJSON(
                '/users/groups',
                data, 
                function (json) {
                    var jsonData = JSON.parse(JSON.stringify(json));
                    var result=jsonData.results;
                    $("#DTE_Field_group_id").html('')
                    for (var i = 0; i < jsonData.length; i++) {
                        var id=jsonData[i]['id'];
                        var text=jsonData[i]['text'];
                        selected=jsonData[i]['selected'];
                       $("#DTE_Field_group_id").append("<option value='"+id+"' "+ selected + " >"+text+"</option>"); 
                     }
            });
    }); 
    
    
    editor.on('initCreate', function (e, node, data, items, type) {
        $.getJSON(
                '/users/groups',
                data, 
                function (json) {
                    var jsonData = JSON.parse(JSON.stringify(json));
                    var result=jsonData.results;
                    $("#DTE_Field_group_id").html('')
                    for (var i = 0; i < jsonData.length; i++) {
                        var id=jsonData[i]['id'];
                        var text=jsonData[i]['text'];
                        selected=jsonData[i]['selected'];
                        $("#DTE_Field_group_id").append("<option value='"+id+"' "+ selected + " >"+text+"</option>"); 
                     }
            });
    }); 
    



    $("#passchangeform").on("submit", function(e) {

        $(".error").hide();
        var hasError = false;
        var password = $("#password").val();
        var password1 = $("#password1").val();
        console.log("validating "+ password + " and  "+ password1);
        if (password == '') {
            $("#password").after('<span class="error text-danger"><em>Please enter a password.</em></span>');
            hasError = true;
        } else if (password1 == '') {
            $("#password1").after('<span class="error text-danger"><em>Please re-enter your password.</em></span>');
            hasError = true;
        } else if (password != password1) {
            $("#password1").after('<span class="error text-danger"><em>Passwords do not match.</em></span>');
            hasError = true;
        }

        if (hasError == true) {
            return false;
        }
        if (hasError == false) {
            console.log("Validation success, Posting")
            var postData = $(this).serializeArray();
        //    var formURL = $(this).attr("action");
            $.ajax({
                url: '/users/setpassword',
                type: "POST",
                data: postData,
                 beforeSend: function (xhr) { // Add this line
                   xhr.setRequestHeader('X-CSRF-Token', csrfToken );
               },
                success: function(data) {
                    var obj = JSON.parse(data);
                    var status=obj.status;
                    var msg=obj.msg;
                    if( status == "Success"){
                        toastr.success(msg);
                        $('#password-modal').modal('hide')
                    }else{
                        toastr.error(msg);
                    }
                },
                error: function(jqXHR, status, error) {
                    console.log(status + ": " + error);
                }
            });
            e.preventDefault();
        }
    });

    $("#submitForm").on('click', function() {
        $("#updateForm").submit();
    });




 
    
  }); //End of defautl function.
  
  function passchange(){
    var table = $('#tblusers').DataTable();
    $('#user_id').val(table.row('.selected').id());
    $('#password-modal').modal({backdrop: 'static', keyboard: false}) ;       
   }

