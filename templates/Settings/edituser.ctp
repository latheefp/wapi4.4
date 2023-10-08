<?php
//echo debug ($areas);
//echo debug ($groups);
?>


<!-- Input addon -->
          <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Register new user</h3> <div id="warningmsgdiv"></div>
            </div>
            <div class="box-body">
                <form role="form"  id="addnewform" action="#" method="post">
                <div class="row form-group">
                    <div class="col-md-4">
                        <label for="exampleInputEmail1">First Name</label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-user"></i></span>
                           <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name">
                         </div>
                   </div>
                   <div class="col-md-4">
                       <label for="exampleInputEmail1">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                           <input  class="form-control" type="text" name="last_name" id="last_name" placeholder="Last name">
                         </div>
                   </div>
                    <div class="col-md-4">
                       <label for="exampleInputEmail1">Email</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                           <input  class="form-control" type="email" name="username" id="username" placeholder="email">
                         </div>
                   </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-4">
                        <label for="exampleInputEmail1">Password</label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-key"></i></span>
                           <input type="password" class="form-control" name="password" id="password" type="password" placeholder="Password">
                         </div>
                   </div>
                   <div class="col-md-4">
                        <label for="exampleInputEmail1">Retype Password</label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-key"></i></span>
                           <input type="password" class="form-control" name="password1" id="password1" placeholder="Retype Password">
                         </div>
                   </div>
                    <div class="col-md-4">
                        <label for="exampleInputEmail1">Mobile No.</label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                           <input type="text" class="form-control" name="phone" id="phone"  placeholder="Phone">
                         </div>
                   </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-4">
                        <label>Area</label>
                        <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-anchor"></i></span>
                        <select id="area_id" name="area_id" class="form-control">
                            <?php
                             foreach ($areas as $key =>$val){
                                 print "<option value=$key>$val</option>";
                             }
                            ?>
                        </select>
                        </div>
                   </div>
                    <div class="col-md-4">
                        <label>Group</label>
                        <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-anchor"></i></span>
                        <select id="group_id" name="group_id[]" class="form-control selectpicker"  multiple data-selected-text-format="count > 3">
                            <?php
                             foreach ($groups as $key =>$val){
                                 print "<option value=$key>$val</option>";
                             }
                            ?>
                        </select>
                        </div>
                   </div>
                
                    
                </div>    
                <div class="row form-group">
                    <div class="col-md-3">
                        <label for="exampleInputEmail1">Branch</label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-location-arrow"></i></span>
                           <input class="form-control" type="text" name="branch" id="branch" placeholder="Branch">
                         </div>
                   </div>
                    <div class="col-md-3">
                        <label for="exampleInputEmail1">Building No.</label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-building"></i></span>
                           <input class="form-control"  name="building_no" id="building_no" type="textarea" placeholder="Building No.">
                         </div>
                   </div>
                    <div class="col-md-6">
                        <label for="exampleInputEmail1">Landmark</label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-map-signs"></i></span>
                           <input class="form-control" name="landmark" id="landmark" type="text" placeholder="Landmark">
                         </div>
                   </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                   <label for="exampleInputEmail1">Map</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-map-pin"></i></span>
                        <input type="text" id="map" name="map" class="form-control">
                            <span class="input-group-btn">
                              <button type="button" onclick="openmap();" class="btn btn-info btn-flat">Lanch!</button>
                            </span>
                    </div>
                   </div>
                </div>
               <!-- /input-group -->
               <button type='submit' class='primary pull-right'>Submit</button>
               </form>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
          
          
          <?php $this->start('scriptBotton'); ?>
<script>
  $(function () {

        $('#group_id').multiselect();

      
      
      
      
  //  $("#tablegroup").DataTable();
  $('#addnewform').formValidation({
            framework: 'bootstrap',
            excluded: ':disabled',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                username: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide email'
                        },
                        email: {
                            message: 'Must be email.'
                        },
                         remote: {
                            enabled: true,
                            url: '/settings/validateform/username',
                            type: 'POST',
                            delay: 300     // Send Ajax request every 2 seconds
                        },

                    }
                },
                password: {
                    validators: {
                        identical: {
                            field: 'password1',
                            message: 'The password and its confirm are not the same'
                        }
                    }
                },
                password1: {
                    validators: {
                        identical: {
                            field: 'password',
                            message: 'The password and its confirm are not the same'
                        }
                    }
                },
                first_name: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide the value'
                        },
                    },
                },
                location: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide the value'
                        },
                    }, 
                },
                area_id: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide the value'
                        },
                    }, 
                },
                
                phone: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide the value'
                        },
                    }, 
                }
                    
                    
                    
                
                
            } //end of fields
    })
    .on('success.form.fv', function(e) {
           e.preventDefault();
            submitform();
        });
    
    
    
//  });
  

    
  function submitform(){
        $('#warningmsgdiv').html('');
        //$('#error-msg').html('');        
        var data = $( "form#addnewform" ).serialize();
        //var url=$('#serverform').attr('defaction');
        var url="/settings/addusers";
        $.ajax({
               type: "POST",
               data:data,
               url: url, 
               success: function(msg){
                 //  console.log(msg);
                   var obj = JSON.parse(msg);
                   var status=obj.status;
                   var msg=obj.message;
                  if(status=="Failed"){
                        $('#error-msg').html('<span class="label label-danger" style="font-size:10px; font-weight:100;">'+msg+'</span>');
                  }else{
                       $('#warningmsgdiv').show();
                       $('#warningmsgdiv').html('<span class="label label-success" style="font-size:10px; font-weight:100;" id="warningmsg">'+msg+'</span></div>');
                       //$('#add-server').modal('hide')
                      //  var table = $('#servertable').DataTable();
                  //      table.ajax.reload();
                    //  table.draw(false);
                       $('#warningmsgdiv').fadeOut(5000);
                  }
               }
               });
               
            }
        
  });
  function openmap(){
                            var popup = window.open();
                            popup.document.write($('#map').val());
                        }
                        
</script>
<?php $this->end(); ?>