<?php

namespace App\View\Helper;

//use Cake\Controller\Controller;
use Cake\View\Helper;
use Cake\View\View;
use Cake\ORM\TableRegistry;

class AccountmenuHelper extends Helper {

    public function initialize(array $config): void {
        //     debug($config);
    }

    function buildlist($option = []) {
     //   debug($option);
        $outputHtml = null;
        $table = \Cake\ORM\TableRegistry::getTableLocator()->get('Accounts');
       // $session = $this->request->getSession();
        $selected=$table->get($option['selected']);
      //  debug($selected);
        $query = $table->find()->all();
        $outputHtml = $outputHtml . '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"   orole="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $outputHtml = $outputHtml . ' <div id="selected_account">'.$selected->company_name.'</div></a>';
        $outputHtml = $outputHtml . ' <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink" >';
        foreach ($query as $key => $val) {
            $outputHtml = $outputHtml . '<a class="dropdown-item" onclick="switchcompany(' . $val->id . ')">' . $val->company_name . '</a>';
        }
        $outputHtml = $outputHtml . " </div>";
      //  debug($outputHtml);
         return $outputHtml;
    }
    
    

}
