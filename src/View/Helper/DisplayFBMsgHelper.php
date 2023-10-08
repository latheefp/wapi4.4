<?php

namespace App\View\Helper;

//use Cake\Controller\Controller;
use Cake\View\Helper;
use Cake\View\View;

class DisplayFBMsgHelper extends Helper {

    public function initialize(array $config): void {
        //     debug($config);
    }

    function format($message = []) {
        $result = null;
        debug($message);
        return $result;
    }

}
