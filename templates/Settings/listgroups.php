<?php
 $this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']],
    ['title' => 'Groups', 'url' => ['controller' => 'Settings', 'action' => 'listgroups']]
]);
?>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12 col-12">
          <div class="box">
            <div class="box-header">
              <div class='col-md-10' >
                    <button onclick="addnew();" class="btn btn-success pull-right btn-sm" id='new'><i class="fa fa-plus">New Group</i></button>
              </div>
             <div class="card card-info col-sm-6">
                 <div class="card-body well" id='addformdiv' style="display:none">
                    <form role="form" id="groupadd-form" class="form-inline col-md-12 was-validated">
                         <div class="form-group col-md-8">
                              <label for="selgroup" class="col-form-label col-md-6">New Group Name:</label>
                              <div class="col-md-6">
                                  <input type="text" name="groupname" id='datafield' required class="form-control input-sm" placeholder="New..">
                                    <div class="valid-feedback">Valid.</div>
                                    <div class="invalid-feedback">Please fill out this field.</div>
                              </div>
                              <input type="hidden" value="test" name="test">
                         </div>
                        <div class="form-group float-right col-md-4">
                            <button type="button" id="groupadd-btn" class="btn  btn-success btn-sm btn-outline ">Create Group</button>
                        </div>
                    </form>
           </div>
        </div>  
              
            <!-- /.box-header -->
            <div class="box-body">
              <table id="tablegroup" class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                  <th class='col-md-1'>Group ID</th>
                  <th class='col-md-3'>Name</th>
                  <th class='col-md-1'>Members</th>
                  <th class='col-md-3'>Action</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($groups as $key =>$val){
                        print '<tr>';
                        print '<td>'.$val->toArray()['id'].'</td>';
                        print '<td>'.$val->toArray()['groupname'].'</td>';
                        print '<td></td>'; ?>
                        <td>
                            <a href="/settings/groupview/<?php echo $val->toArray()['id']; ?>"><button class="btn btn-xs btn-info"><span class="glyphicon glyphicon-zoom-in"></span> View</button></a>&nbsp;
                            <a href="/settings/updategroup/<?php echo $val->toArray()['id']; ?>"><button class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span> Edit</button></a>&nbsp;
                            <a href="/settings/permissions/<?php echo $val->toArray()['id']; ?>"><button class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span>Permissions</button></a>&nbsp;
                            <a href="/settings/deletegroup/<?php echo $val->toArray()['id']; ?>" onclick="return confirm('Are you sure?')"><button class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove-circle"></span> Delete</button></a>&nbsp;
                        </td>
                     <?php
                     print '</tr>';
                    }
                    ?>
                </tbody>
                <tfoot>
           
                </tfoot>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->


  <?php $this->Html->scriptStart(['block' => true]);?>
  <!--<script>-->
   $(document).ready(function(){  

   
    $('#tablegroup').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "pageLength": <?php echo $pagination_count; ?>
    });
    
    

    
    
    
             });   //end of DR.
             
   $("#groupadd-btn").click(function(event){
   //     alert ("licked");
        var form = $("#groupadd-form")
           // console.log("Button clicked");
            if (form[0].checkValidity() === false)
                {
                    ajaxvalidate(event);      
                }else{
                     ajaxvalidate(event);
                }
        });
  
  function addnew(){
        $('#addformdiv').show();
    };
    
  function submitform(){
        $('#warningmsgdiv').html('');
        //$('#error-msg').html('');        
        var data = $( "form#addnewform" ).serialize();
        //var url=$('#serverform').attr('defaction');
        var url="/settings/groupadd";
        $.ajax({
               type: "POST",
               data:data,
               url: url, 
               success: function(msg){
                 //  console.log(msg);
                   var obj = JSON.parse(msg);
                   var status=obj.status;
                   var msg=obj.message;
                  if(status=="error"){
                        $('#error-msg').html('<span class="label label-danger" style="font-size:10px; font-weight:100;">'+msg+'</span>');
                  }else{
                       $('#warningmsgdiv').show();
                       $('#warningmsgdiv').html('<span class="label label-success" style="font-size:10px; font-weight:100;" id="warningmsg">'+msg+'</span></div>');
                       $('#add-server').modal('hide')
                        var table = $('#servertable').DataTable();
                  //      table.ajax.reload();
                      table.draw(false);
                    // table.fnStandingRedraw();
                       $('#warningmsgdiv').fadeOut(5000);
                  }
               }
               });
               
            }




function ajaxvalidate(event){
     var form = $("#groupadd-form")
       $.ajax({
            beforeSend: function (xhr) { // Add this line
                   xhr.setRequestHeader('X-CSRF-Token', csrfToken);
               },
           url : "/settings/validate/add/Groups",
           method: "POST",
         //  async:false,
           data:form.serialize(),
           //success: successCallBack
           })
            .done(function(data){
                 var jsonData = JSON.parse(data);
                 var validStatus=true; 
                 for (var i = 0; i < jsonData.length; i++) {
                     var counter = jsonData[i];
                     var inputID=counter.field;
                     var msg=counter.error;
                     var input = document.getElementById(inputID);
                     input.classList.add('is-invalid');
                     input.setCustomValidity(msg);
                     input.reportValidity();
                     validStatus=false;
                     input.reportValidity();
                 }
                if( validStatus == false){
                     event.preventDefault()
                     event.stopPropagation()
                }else{
                    submitregister();
                }
            });
           }
    
<?php $this->Html->scriptEnd(); ?>

