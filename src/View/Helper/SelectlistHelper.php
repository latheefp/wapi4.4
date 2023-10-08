<?php

namespace App\View\Helper;

//use Cake\Controller\Controller;
use Cake\View\Helper;
use Cake\View\View;
use Cake\ORM\TableRegistry;

class SelectlistHelper extends Helper {

    public function initialize(array $config): void {
        //     debug($config);
    }

    function buildlist($option = []) {
        $outputHtml = null;
        $table = \Cake\ORM\TableRegistry::getTableLocator()->get($option['table']);
        $fname = $option['field'];
        $select = $option['selected'];
        if (isset($option['where'])) {
            $where = $option['where'];
        } else {
            $where = array();
        }
        if (isset($option['value'])) {
            $value = $option['value'];
        } else {
            $value = "id";
        }
        $query = $table->find()
                ->select([$value, $fname])
                ->where($where)
                ->toList();
        
        
     //   print_r($query);

        foreach ($query as $key => $val) {
            $id = $val->$value;
            $field = $val->$fname;
            if ($select == $id) {
                $selected = 'selected="selected"';
            } else {
                $selected = null;
            }
            $outputHtml = $outputHtml . '<option value="' . $id . '" ' . $selected . '>' . $field . '</option>';
        }
        return $outputHtml;
    }

}
