<?php
//pr($fieldsType);
$data['recordsFiltered']=$this->Paginator->params()['count'];
$data['recordsTotal']=$this->Paginator->params()['current'];
$data['data']=array();
foreach ($users as $key =>$val){
    $row=array();
    $row['DT_RowId']=$val->id;
    foreach($fieldsType as $fkey =>$fval){
        if($fval['viewable'] ==true){
            $field=$fval['fld_name'];
            //$fld=$fval['fld_name'];
            $row[$field]=$this->Dformat->format(
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