<?php
 $this->Breadcrumbs->add([
    ['title' => 'Home', 'url' => ['controller' => 'Dashboards', 'action' => 'index']],
    ['title' => 'Groups', 'url' => ['controller' => 'Settings', 'action' => 'listgroups']]
]);
?>
    <div class="card card-info col-sm-6">
        <div class="card-body well">
            <form role="form" class="form-inline col-md-12">
                 <div class="form-group col-md-8">
                      <label for="selgroup" class="col-form-label col-md-6">Select Group:</label>
                      <div class="col-md-6">
                          <select class="form-control" id="selgroup" onchange="location = '/settings/permissions/'+this.options[this.selectedIndex].value;">
                          <?php
                             foreach($groups as $key =>$val){
                                 if($val->id== $group_id){
                                     $select="selected";
                                 }else{
                                     $select="";
                                 }
                                 print '<option value='.$val->id.' '.$select.'>'.$val->groupname.'</option>';
                             }
                             ?>
                          </select>
                      </div>
                 </div>
                <div class="form-group float-right col-md-4">
                      <button type="button" id="btnsubmit" class="btn  btn-success btn-sm btn-outline ">Submit Change</button>
                </div>
            </form>
           </div>
        </div>
<!--</nav>-->
<br>
<!-- Main content -->
<section class="content" >
   <div class="row">
      <!-- left column -->
      <div class="col-md-6">
         <!-- general form elements -->
         <div class="box box-info">
            <div class="box-header with-border">
               <div class="row">
                  <?php 
                     echo $this->Form->create(null,
                     [
                     'type'=>'post',
                     'class'=>'',
                     'url'=>'#',
                     'id'=>'frmpermission',
                     'class'=>'col-12 form '    
                         
                         
                     ]
                     );
                     
                     ?>
                  <input name="group_id" value="<?php echo $group_id;?>" style="display:none">
                  <?php foreach($action_types as $actiontype){ 
                     $class=str_replace(' ', '', $actiontype->category); ?>
                  <div class="card card-info">
                      <div class="card-header card-head-small">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                  <?php echo $actiontype->category; ?>
                            </div>
                            <div class="col-6 col-md-6 float-right">
                                <input  type="checkbox" class="<?php echo $class; ?>" onClick="checkuncheckall(this,this.checked);">
                                  Select All/Unselect All.
                                  </label>  
                            </div>  
                        </div>
                      </div>
                    <div class="card-body">
                       <div class="form-group">
                          <?php
                             // echo debug ($group_permission);
                              foreach ($permissionarray as $permission){
                                  if($permission->category==$actiontype->category){?>
                              <?php 
                                 if(isset($group_permission[$permission->id])){
                                     $checked="checked";
                                 }else{
                                      $checked="";
                                 }
                                 ?>
                              <div class="checkbox ">
                                 <input name="<?php 
                                    print $permission->id; ?>" type="checkbox" class="<?php echo $class; ?> "<?php print $checked;?>>
                                 <?php 
                                    print $permission->description; ?>
                              </div>
                          <?php   }
                             }
                             ?>
                       </div>
                    </div>
                  </div>
                  <?php } ?>
                  </form>
                  <!-- /.box-body -->
               </div>
            </div>
         </div>
      </div>
      <!--        End of col-md-6-->
   </div>
</section>
<script>
   <?php $this->Html->scriptStart(['block' => true]);?>
   $(document).ready(function(){
        $('#btnsubmit').click(function(){
         //   alert("clicked");
            
          
           var data = $( "form#frmpermission" ).serialize();
           var url="/settings/submitpermission";
           console.log(data);
           $.ajax({
                  type: "POST",
                  data:data,
                  url: url, 
                   beforeSend: function (xhr) { // Add this line
                       xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                   },  // Add this line
                 
                  success: function(msg){
                    //  console.log(msg);
                      var obj = JSON.parse(msg);
                      var status=obj.status;
                      var msg=obj.message;
                     if(status=="error"){
                           toastr.error(msg);
                     }else{
                          toastr.success(msg);
                     }
                  }
                  });
            
        }) ;  
       
   });
         
         function check_ucheck_usersettings(isChecked) {
          //   console.log("clicked settings");
   	if(isChecked) {
   		$('.usersettings').each(function() { 
   			this.checked = true; 
   		});
   	} else {
   		$('.usersettings').each(function() {
   			this.checked = false;
   		});
   	}
       } ;
       
       function checkuncheckall(e,isChecked){
           var eclass=e.getAttribute('class');
           
           if(isChecked) {
   		$('.'+eclass).each(function() { 
   			this.checked = true; 
   		});
   	} else {
   		$('.'+eclass).each(function() {
   			this.checked = false;
   		});
   	}
       }
   <?php $this->Html->scriptEnd(); ?>
    
</script>

<style>
     .card-head-small{
        padding-top: 0.5rem;
        padding-bottom: 0.75rem;
        line-height:.5
    }
    

</style>