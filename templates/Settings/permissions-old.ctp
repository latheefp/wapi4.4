<section class="content-header">
    <div class="row">
            <h3>
        Permissions
        <small>Preview</small>
      </h3>
        <form role="form" class="form-inline">
        <div class="form-group col-md-7">
                  <label>Select Group</label>
                  <select class="form-control" id="selgroup" onchange="location = '/settings/permissions/'+this.options[this.selectedIndex].value;">
                      <option>Select Group</option>
                    <?php
                    foreach($groups as $key =>$val){
                        if($group_id==$key){
                            $select="selected";
                        }else{
                            $select="";
                        }
                        print '<option value='.$key.' '.$select.'>'.$val.'</option>';
                    }
                    ?>
                  </select>
                 <button type="button" id="btnsubmit" class="btn  btn-primary">Submit Change</button>
       </div>  
            <div id="warningmsgdiv"></div>
    </form>
      
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol>
        
        
    </div>
    
  
</section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-6">
          <!-- general form elements -->
            <div class="box box-info">
            <div class="box-header with-border">
                <div class="row">
                    <form role="form" id="frmpermission">
                        <input name="group_id" value="<?php echo $group_id;?>" style="display:none">
                    <div class="form-group">
                        <label for="" class="col-md-6 control-label">User Settings</label>
                        <div class="checkbox" id="usersettings">
                          <label>
                            <input  type="checkbox" onClick="check_ucheck_usersettings(this.checked);">
                            Select All/Unselect All.
                         </label>
                      </div>
                    </div>
                    <!-- /.box-header -->
                <div class="box-body">
                
                  <div class="form-group">
                      <?php
                          foreach ($permissions as $key =>$val){
                              if($val['category']=="User Management"){?>
                                  <?php 
                                  if(isset($group_permission[$val['id']])){
                                      $checked="checked";
                                  }else{
                                       $checked="";
                                  }
                                  ?>
                                  <div class="checkbox ">
                                  <label>
                                    <input name="<?php print $val['id']; ?>" type="checkbox" class="usersettings"<?php print $checked;  ?>>
                                    <?php print $val['permission']; ?>
                                  </label>
                                </div>
                           <?php   }
                          }
                      ?>
                  </div>
               
              </div>
                    
                    
                    
                    <div class="form-group">
                        <label for="" class="col-md-6 control-label">Site Settings</label>
                        <div class="checkbox" id="usersettings">
                          <label>
                            <input  type="checkbox" onClick="check_ucheck_sitesettings(this.checked);">
                            Select All/Unselect All.
                         </label>
                      </div>
                    </div>
                    <!-- /.box-header -->
                <div class="box-body">
                
                  <div class="form-group">
                      <?php
                          foreach ($permissions as $key =>$val){
                              if($val['category']=="Site Settings"){?>
                                  <?php 
                                  if(isset($group_permission[$val['id']])){
                                      $checked="checked";
                                  }else{
                                       $checked="";
                                  }
                                  ?>
                                  <div class="checkbox ">
                                  <label>
                                    <input name="<?php print $val['id']; ?>" type="checkbox" class="sitesettings"<?php print $checked;  ?>>
                                    <?php print $val['permission']; ?>
                                  </label>
                                </div>
                           <?php   }
                          }
                      ?>
                  </div>
               
              </div>
                     </form>
              <!-- /.box-body -->
              </div>
        </div> 
            </div>
        </div>
<!--        End of col-md-6-->
      </div>
    </section>
 <?php $this->start('scriptBotton'); ?>
  <script>

      function check_ucheck_usersettings(isChecked) {
          console.log("clicked settings");
	if(isChecked) {
		$('.usersettings').each(function() { 
			this.checked = true; 
		});
	} else {
		$('.usersettings').each(function() {
			this.checked = false;
		});
	}
    }   
        
        function check_ucheck_sitesettings(isChecked) {
          console.log("clicked sitesettings");
	if(isChecked) {
		$('.sitesettings').each(function() { 
			this.checked = true; 
		});
	} else {
		$('.sitesettings').each(function() {
			this.checked = false;
		});
	}
    }   
        
     $('#btnsubmit').click(function(){
         
         
         
        $('#warningmsgdiv').html('');
        //$('#error-msg').html('');        
        var data = $( "form#frmpermission" ).serialize();
       // var group=$('#selgroup').options[this.selectedIndex].value;
        //var url=$('#serverform').attr('defaction');
        var url="/settings/submitpermission";
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
         
         
         
         
         
     }) ;  
        
  </script>
  
  <?php
  $this->end();
  ?>