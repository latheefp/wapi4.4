<?php
//debug($data);
$result=[];
foreach ($data as $key =>$val){
    $result['labels'][]=$val['_matchingData']['Schedules']['name'];
    $result['data']['total'][]=$val['count'];         
}
echo json_encode($result);