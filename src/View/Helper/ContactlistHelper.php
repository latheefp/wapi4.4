<?php
namespace App\View\Helper;
//use Cake\Controller\Controller;
use Cake\View\Helper;
use Cake\View\View;
use Cake\ORM\TableRegistry; 

class ContactlistHelper extends Helper
{
    public function initialize(array $config): void
    {
   //     debug($config);
    }
    
    function buildlist($option=[]){ 
        $outputHtml="";
     
        foreach ($option as $key =>$val){
           
       //    $outputHtml=$outputHtml.'<option value="'.$id.'" ' . $selected. '>'.$field.'</option>';
        }
          return $outputHtml;
    }
  
}