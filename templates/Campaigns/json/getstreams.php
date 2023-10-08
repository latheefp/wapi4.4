<?php

//debug($data); 
//print_r($fieldsType);
$result = [];
$result['recordsFiltered'] = $this->Paginator->params()['count'];
$result['recordsTotal'] = $this->Paginator->params()['current'];
$result['data'] = array();
foreach ($data as $key => $val) {
    $row = array();
    $row['DT_RowId'] = $val->id;


    foreach ($fieldsType as $fkey => $fval) {
        if ($fval['viewable'] == true) {
            $field = $fval['fld_name'];
            $title = $fval['title'];
            $row[$title] = $this->Dformat->format(
                    [
                        'data' => $val->$field,
                        'format' => $fval['format'],
                        'boolean_yes' => $fval['boolean_yes'],
                        'boolean_no' => $fval['boolean_no'],
                    ]
            );
        }
    }
    $row['commented'] = $val->commented;
    $result['data'][] = $row;
}
echo json_encode($result);
