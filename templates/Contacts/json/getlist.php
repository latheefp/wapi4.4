<?php

$result=[];
$result['recordsFiltered']=$this->Paginator->params()['count'];
$result['recordsTotal']=$this->Paginator->params()['current'];
$result['data']=array();

foreach($data as $key =>$val){
 //   debug($val);

     $row=array();
     $row['DT_RowId']=$val->id;
     $row['Name']=$val->name;
     $row['Count']=$val->contact_count;

     $result['data'][]=$row;
 }
 echo json_encode($result);
