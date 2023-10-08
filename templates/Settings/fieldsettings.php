<?php

//debug($groups);
?>

<section class="content" style="font-size:90%;">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
            </div>
            <!-- /.box-header -->
            <div class="box-body">
<div class="panel panel-default">
    <div class="panel-body">
<!--        <form role="form" class="form-inline">  -->
        <!--<div class="dropdown  btn-group" role="group">-->
        <div class="btn-group form-inline" role="group">
        <div class="form-group">
            <label for="sel1">Table</label>
            <select class="form-control"  onchange="changeit()"  id="tableselect">
              <?php
              foreach ($tables as $key => $val){
                    print '<option value='.$val.'> '.$val. '</options>';
                }
                ?>
            </select>
         </div>
        
        
         <div class="form-group">
            <label for="sel1">Group</label>
            <select class="form-control" onchange="changeit()"  id="groupselect">
              <?php
              foreach ($groups as $key => $val){
                    print '<option value='.$val->id.' > '.$val->groupname. '</options>';
                }
                ?>
            </select>
         </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" onclick="submitchanges();" id="submitbtn">Submit</button>
            </div>
            
            <div class="form-group">
            <label for="sel1">Copy to Group</label>
            <select class="form-control"  id="target_groupselect" onchange="changeit()" >
              <?php
              foreach ($groups as $key => $val){
                    print '<option value='.$val->id.' > '.$val->groupname. '</options>';
                }
                ?>
            </select>
         </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" onclick="copy_permissions();"  id="submitbtn">Copy</button>
            </div>
            
        </div>
    </div>    
</div>
<div id="logs"></div>
<div  style="margin-left:15px; padding-bottom:20px;">
    <div class="show-grid row">
        <div class="col-md-2 h5">Table</div>
        <div class="col-sm-1 h5">Read <input type="checkbox" id="selectallread" /></div>
        <div class="col-sm-1 h5">Edit<input type="checkbox" id="selectalledit" /></div>
        <div class="col-sm-1 h5">Get Data<input type="checkbox" id="selectall_get_data" /></div>
        <div class="col-sm-1 h5">Order</div>
    </div> 
    <form id="tabletray" >
        
    </form>    
</div>
            </div>
          </div>
        </div>
      </div>
</section>
            

<style>
      
 .show-grid [class^=col-] {
    margin-bottom:0px !important; 
    margin-top:0px !important; 
    padding-top: 2px;
    padding-bottom: 2px;
    background-color: #eee;
    background-color: rgba(86,61,124,.15);
    border: 1px solid #ddd;
    border: 1px solid rgba(86,61,124,.2);
    height: 25px;
}
.show-grid input[type="checkbox"] {
    margin: 2px 0 0;
    line-height: normal;
/*    margin: 0;*/
}

.input-small{
    padding-top:0px  !important;
    padding-bottom: 0px  !important;
    padding-left: 0px;
    padding-right: 0px;
    width: 40px;
    height: 18px;
    border: 0px;
    
}
</style>




<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
 $(document).ready(function(){
    $(function(){
//         $(".tableselect").on("click", "li", function(){
//              var value=($('#tabledd').text());
//              alert (value);
//              var group=$('#groupselect').text();
//              console.log(group);
//           //   $.ajax({ url: '/permissions/gettablepermissions/'+$value}) .done(function( logs ) { $('#location').html(logs);});
//         })
     })
     
    $("#selectallread").click(function(){
       //         console.log("selectallread cliked");
                $('.readcheckbox').prop('checked',$(this).prop('checked'));
            });
     
//      $('.readcheckbox').click(function(){
//                alert("Running");
//                if($(".readcheckbox").length==$(".readcheckbox:checked").length)
//                {
//                    $("#selectallread").prop('checked');
//                }else{
//                    alert ("Uncheck");
////                    $("#selectallread").prop('checked);
//                }
//            });
       
        $("#selectalledit").click(function(){
       //         console.log("selectallread cliked");
                $('.editcheckbox').prop('checked',$(this).prop('checked'));
            });
            
        $("#selectall_get_data").click(function(){
       //         console.log("selectallread cliked");
                $('.get_data_checkbox').prop('checked',$(this).prop('checked'));
            });
            
    changeit();
    
    }); //End of Document Ready.
     
     
    
 //});   
 
 function changeit(){
     $('#logs').html("");
     group=$('#groupselect').val();
     table=$('#tableselect').val();
     $.ajax({ url: '/settings/gettablepermissions?table='+table+'&group='+group}) .done(function( logs ) { 
         $("#selectallread").prop("checked", false);
         $("#selectalledit").prop("checked", false);
         $("#selectall_get_data").prop("checked", false);
         $('#tabletray').html(logs);
     });
 }
 
 function submitchanges(){
    var data = $( "form#tabletray" ).serialize();
    var group = $('#groupselect').val();
  console.log(data);
    
    $('#logs').html("");
    $.ajax({
        type: "POST",
        cache: false,
        async: true,
        data:data,
        //data: {data:data, group:group},
        url: "<?php echo $this->Html->url("/settings/update_permission"); ?>", 
        success: function(msg){
            // console.log(msg);
           $('#logs').html(msg);
           if(msg.match(/class="error"/g)){
              
               $('#errormessage').html(msg);
           }else{
               $('#testdata').html(msg);
               $('#dialog-edit').dialog('close');
           }
            //alert (msg);
            }
        });
   
   
   
 }
 
  function copy_permissions(){
    var tablename = $( "#tableselect" ).val();
    var group = $('#groupselect').val();
    var target_group = $('#target_groupselect').val();
    $('#logs').html("");
    $.ajax({
        type: "POST",
        cache: false,
        async: true,
        data: {table:tablename, group:group,target_group:target_group},
        url: "<?php echo $this->Html->url("/settings/copy_permission"); ?>", 
        success: function(msg){
                var obj = JSON.parse(msg);
                var msg=obj.msg;
                var status=obj.status;
               
                if(status == "success"){
                    toastr['success'](msg);
                }else{
                     toastr['error'](msg);
                }
            }
    
        });
   
   
   
 }
 
//</script>
<?php $this->Html->scriptEnd(); ?>
    