<?php

$result = [];
$result['recordsFiltered'] = $this->Paginator->params()['count'];
$result['recordsTotal'] = $this->Paginator->params()['current'];
$result['data'] = array();
foreach ($data as $key => $val) {
    $row = array();
    $row['DT_RowId'] = $val->id;
    foreach ($fieldsType as $fkey => $fval) {
        if ($fval['viewable'] == true) {
        //    print_r($fval);
            $field = $fval['fld_name'];
            $title = $fval['title'];
            $row[$title] = $this->Dformat->format(
                    [
                        'data' => $val->$field,
                        'format' => $fval['format'],
                        'bool_yes'=>$fval['boolean_yes'],
                        'bool_no'=>$fval['boolean_no']
                    ]
            );
        }
    }
    $result['data'][] = $row;
}
echo json_encode($result);
