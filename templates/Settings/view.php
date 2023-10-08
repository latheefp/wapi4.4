                <?php  //echo debug ($permission); ?>
<?= $this->element('pos_menu');?>
<section class="content">
      <div class="row">
        <div class="col-xs-12 col-md-12">
          <div class="box">
            <div class="box-header">
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="<?= $view ?>_table" class="table table-bordered table-striped" style="">
                    <thead>
                            <tr>
                           <?php     
                            foreach ($permission as $key =>$val){
                                print'<th>' .$val['title'] .'</th>';
                            }?>
                            </tr>
                    </thead>
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



<script>
   var editor; 
   
  $(function () {
    editor = new $.fn.dataTable.Editor( {
//ajax: "/users/edit",
ajax: {
           "url": "/inventories/edittable/<?= $view ?>",
//           data: function(d){
//               d.views=
//           }     
        }, 
table: "#<?= $view ?>_table",
fields: [ 
      
    <?php
    foreach ($permission as $key =>$val){
        $format=$val['format'];
        if(($val['edit']==true)&&($val['readonly']==false)){
            switch ($format) {
                case 'boolean':?>
                {
                   "label":"<?= $val['title'] ?>",
                   "name":"<?= $val['field'] ?>",
                   type:  "radio",
                    options: [
                        { label: "No", value: 0 },
                        { label: "Yes",value: 1 }
                    ],
                },
               <?php 

                    break;
                case 't2d':  
                    break;
                case 'd2d':  
                    break;
                case 'local_select':  ?>
                    { 
                        label:"<?= $val['title']?>",
                        name:"<?= $val['field']?>",
                        type:"select",
                        options:[
                           <?php
                            $selectarray=explode(',',$val['data_type_csv']);
                            foreach ($selectarray as $key =>$val){ ?>
                                { label:"<?= $val ?>", value:"<?= $val ?>"},  
                        <?php   }
                           ?>
                        ]
                    },

              <?php      break;
                 case 'local_mselect':  ?>
                    { 
                        label:"<?= $val['title']?>",
                        name:"<?= $val['field']?>",
                        type:"select2",
                        attr: {
                        multiple: "multiple"
                            },
                        options:[
                           <?php
                            $selectarray=explode(',',$val['data_type_csv']);
                            foreach ($selectarray as $key =>$val){ ?>
                                { label:"<?= $val ?>", value:"<?= $val ?>"},  
                        <?php   }
                           ?>
                        ]
                    },

              <?php      break;
              case 'remote_select':  ?>
                    { 
                        label:"<?= $val['title']?>",
                        name:"<?= $val['field']?>",
                        type:"select2",
                        attr: {
//                        multiple: "multiple"
                            },
                        options:[
                           
                        ]
                    },

              <?php      break;
                default:
                   print '{"label":"'.$val['title'].'",';
                   print '"name":"'.$val['field'].'"},';
            }
        }
    }
    ?>

]
    } );    
      
      
      
   $('#<?= $view ?>_table').DataTable({
      "ajax": {
                    "url": "/inventories/getdata/<?= $view ?>",
                    "type": "POST"
                },       
        "stateSave": true,
        dom: 'Bfrtip', 
        "lengthMenu": [[5, 10, 15, 25, 50, 100 ], [5, 10, 15, 25, 50, 100]],
        "processing": true,
        "serverSide": true,
        "pageLength": <?php  print $PageLength; ?>,
        scrollY:        "300px",
        scrollCollapse: true,
        select: true,
        "columns": [
        <?php
        $lastval=end($permission);
        foreach ($permission as $key =>$val){
            $format=$val['format'];
            if($val['view']==true){
                if($val['searchable']==1){$search="true";}else{$search="false";}  
                if($lastval['field']==$val['field']){
                    $comma="";
                }else{
                   $comma=",";
                }
            switch ($format) {
                case 'boolean':
                   // print'{"data":"'.$val['field'].'", "name":"'.$val['field'].'", "width":'.$val['colwidth'].', "searchable":'.$search.'}'.$comma."\n"; ?>
                    {

                        "data": "<?= $val['field']?>",
                        "name": "<?= $val['field']?>",
                        "width": <?= $val['colwidth']?>,
                        "searchable": <?= $search?>, 
                        render: function(data) {
                           if (data === 0) {
                               return '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
                           }
                           else {
                               return '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
                           } 
                        }
                    }
                    
               <?php 
                    print $comma;
                    break;
                case 't2d':  
                    break;
                case 'd2d':  
                    break;
                default:
                    print'{"data":"'.$val['field'].'", "name":"'.$val['field'].'", "width":'.$val['colwidth'].', "searchable":'.$search.'}'.$comma."\n";  
            }
                
                
            

            }
        }
           ?>

        ],
        buttons:[
            { extend: "create", editor: editor },
            { extend: "edit",   editor: editor },
            { extend: "remove", editor: editor },
            { extend: "excelHtml5", editor: editor }
           
    ],
       
        
        })
        


; //End of dT.
<?php
 foreach($permission as $key=>$val){
                $format=$val['format'];
                if($val['edit']==true){
                    switch ($format){
                        case "local_mselect":?>
                            editor.on('initEdit', function (e, node, data, items, type) {
                            $.getJSON(
                                    '/inventories/get_local_mselect?view=<?= $view ?>&field=<?= $key ?>',
                                    data, 
                                    function (json) {
                                        $("#DTE_Field_<?= $key ?>").val(null).trigger('change');
                                        $("#DTE_Field_<?= $key ?>").html('');
                                        var jsonData = JSON.parse(JSON.stringify(json));
                                        var result=jsonData.results;
                                        for (var i = 0; i < jsonData.length; i++) {
                                            var id=jsonData[i]['text'];
                                            var text=jsonData[i]['text'];
                                            var selected=jsonData[i]['selected'];
                                           $("#DTE_Field_<?= $key?>").append("<option value='"+id+"' "+ selected + " >"+text+"</option>"); 
                                         }
                                });
                        });
                           
                        <?php
                        break;
                    case "remote_select":?>
                            editor.on('initEdit', function (e, node, data, items, type) {
                            $.getJSON(
                                    '/inventories/get_remote_select?view=<?= $view ?>&field=<?= $key ?>',
                                    data, 
                                    function (json) {
                                        $("#DTE_Field_<?= $key ?>").val(null).trigger('change');
                                        $("#DTE_Field_<?= $key ?>").html('');
                                        var jsonData = JSON.parse(JSON.stringify(json));
                                        var result=jsonData.results;
                                        for (var i = 0; i < jsonData.length; i++) {
                                            var id=jsonData[i]['text'];
                                            var text=jsonData[i]['text'];
                                            var selected=jsonData[i]['selected'];
                                           $("#DTE_Field_<?= $key?>").append("<option value='"+id+"' "+ selected + " >"+text+"</option>"); 
                                         }
                                });
                        });
                           
                        <?php
                        break;
                    
                    
                    
                  
                    
                    
                }
            }
 }


?>




 
    
  }); //End of defautl function.
  

</script>

