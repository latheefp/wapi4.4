<?php
//pr($result);
if(isset($result['data'])){
    $val=$result['data'][0];
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
    
    echo json_encode(array('data'=>$row));
}else{
  echo json_encode($result);  
}

