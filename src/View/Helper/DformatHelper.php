<?php

namespace App\View\Helper;

//use Cake\Controller\Controller;
use Cake\View\Helper;
use Cake\View\View;

class DformatHelper extends Helper {

    public function initialize(array $config): void {
        //     debug($config);
    }

    function format($option = []) {
        $result = null;
        $oformat = $option['format'];
        $odata = $option['data'];
    //    print_r($option);

        switch ($oformat) {
            case "number":
                if (is_int($odata)) {
                    $result = "$odata";
                } else {
                    $result = $odata;
                }

                break;
            case "email":
                if (!empty($odata)) {
                    $result = $odata;
                }
                break;
            case "DT2DT":
            //    print_r($odata);
                if (!empty($odata)) {
                    $result = $odata->format('d-m-Y H:i:s');
                }
                break;
            case "boolean":
                if ($odata == true) {
                    if (isset($option['boolean_yes'])) {
                        $result = $option['boolean_yes'];
                    } else {
                        $result = '<i class="fas fa-check"></i>';
                    }
                } else {
                    if (isset($option['boolean_no'])) {
                        $result = $option['boolean_no'];
                    } else {
                        $result = '<i class="fas fa-times"></i>';
                    }
                }
//            }
                break;
        //    case "data+bool":
        //        debug($row);
          //      debug($odata);
            //    debug($oformat);
                // if ($odata == true) {
                //     if (isset($option['boolean_yes'])) {
                //         $result = $option['boolean_yes'];
                //     } else {
                //         $result = '<i class="fas fa-check"></i>';
                //     }
                // } else {
                //     if (isset($option['boolean_no'])) {
                //         $result = $option['boolean_no'];
                //     } else {
                //         $result = '<i class="fas fa-times"></i>';
                //     }
                // }

           //     break;    
            case "DT2D":
                if (!empty($odata)) {
                    $result = $odata->format('Y-m-d');
                }
                break;
                case "N2Month":
                    if (!empty($odata)) {
                        $result =  date('F', mktime(0, 0, 0, $odata, 1));
                    }
                    break;    
            case "trim":
                //trim the string to last 40 characters like api keys
                if (!empty($odata)) {
                    $result = $short = substr($odata, -40); // last 40 characters
                }
                break;        
            default:
                $result = $option['data'];
        }
        return $result;
    }

}
