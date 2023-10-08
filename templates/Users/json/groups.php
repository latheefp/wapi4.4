<?php
//pr($users);
$selected=[];
foreach ($users as $key =>$val){
    $selected[$val->group_id]=$val->group_id;
}
//pr($selected);
$result=[];
foreach($groups as $key =>$val){
    $group['id']=$val->id;
    $group['text']=$val->groupname;
    if(isset($selected[$group['id']])){
        $group['selected']="selected";
    }else{
        $group['selected']="";
    }
        
    $result[]=$group;
}
echo json_encode($result);
                                               