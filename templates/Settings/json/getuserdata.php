<?php
//pr($fieldsType);
$data['recordsFiltered']=$this->Paginator->params()['count'];
$data['recordsTotal']=$this->Paginator->params()['current'];
$data['data']=array();
foreach ($users as $key =>$val){
   // print_r($val);
    $row=array();
    $row['DT_RowId']=$val->id;
   // $row['ID']=$val->id;
    foreach($fieldsType as $fkey =>$fval){
        if($fval['viewable'] ==true){
            $field=$fval['fld_name'];
            $title=$fval['title'];
            if(!empty($fval['contains'])){
                  $row[$title]=$this->Dformat->format(
                    [
                'data'=>$val->{$fval['contains']}->{$fval['contains_field']} ?? '',
                'format'=>$fval['format']
                    ]
                    );

            }else{
                $row[$title]=$this->Dformat->format(
                    [
                'data'=>$val->$field,
                'format'=>$fval['format']
                    ]
                    );
            }

          
        }
    }
     $data['data'][]=$row;
}
echo json_encode($data);
