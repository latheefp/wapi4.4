<?php

$result=[];
$result['recordsFiltered']=$this->Paginator->params()['count'];
$result['recordsTotal']=$this->Paginator->params()['current'];
$result['data']=array();
foreach ($data as $key =>$val){
  //  print_r($val);
    $row=array();
    $row['DT_RowId']=$val->id;
    foreach($fieldsType as $fkey =>$fval){
        if($fval['viewable'] ==true){
            $field=$fval['fld_name'];
            $title=$fval['title'];
            $row[$title]=$this->Dformat->format(
                    [
                'data'=>$val->$field,
                'format'=>$fval['format']
                    ]
                    );
        }
    }
    $row['template_details']=$val['template_details'];
    $result['data'][]=$row;
}
echo json_encode($result);
