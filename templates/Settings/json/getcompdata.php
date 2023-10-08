<?php
$data['recordsFiltered']=$this->Paginator->params()['count'];
$data['recordsTotal']=$this->Paginator->params()['current'];
$data['data']=array();
foreach ($result as $key =>$val){
  //  pr($val);
    $row=array();
    $row['DT_RowId']=$val->id;
   // $row['ID']=$val->id;
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
     $data['data'][]=$row;
}
echo json_encode($data);
//$data);