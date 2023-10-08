<?php
//debug($wa_data);
//debug($no_wa_data);

$result=array();
foreach ($wa_data as $key =>$val){
    $wa_result[$val->timeCreated]['has_wa']=$val->count;
}

foreach ($no_wa_data as $key =>$val){
    $wa_result[$val->timeCreated]['no_wa']=$val->count;
}

//debug($wa_result);
$result=array();
foreach ($wa_result as $key =>$val){
  //  debug($val);
    $result['labels'][]=$key;
    if(isset($val['has_wa'])){
        $result['data']['has_wa'][]=$val['has_wa'];
    }else{
        $result['data']['has_wa'][]=null;
        $val['has_wa']=null;
    }
    
    if(isset($val['no_wa'])){
        $result['data']['no_wa'][]=$val['no_wa'];
    }else{
        $result['data']['no_wa'][]=null;
        $val['no_wa']=null;
    }
    $result['data']['total'][]=$val['has_wa']+$val['no_wa'];
}
echo json_encode($result);