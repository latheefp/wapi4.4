<?php

namespace App\View\Helper;

//use Cake\Controller\Controller;
use Cake\View\Helper;
use Cake\View\View;
use Cake\ORM\TableRegistry as TableRegistry;

class SendDataformatHelper extends Helper
{

    public function initialize(array $config): void
    {
        //     debug($config);
    }

    function format($data)
    {
      //  $sendarray = json_decode($data['json'], true);
        $send_array = json_decode($this->_removeTrailingCommas($data['json']), true);
        // debug($send_array);
        $msg = null;
        switch ($send_array['type']) {
            case "template":
            case "api":
                $template_name = $send_array['template']['name'];
                // debug($template_name);
                $template_info =  TableRegistry::getTableLocator()->get('Templates')->find()->where(['name' => $template_name])->toArray();
    
                // debug($template_info);
                if (!empty($template_info)) {
                    $template_details = json_decode($template_info[0]['template_details'], true);
                    // debug($template_details);
                    $tbutton = '';
                    $tbody = '';
                    $theader = '';
                    if (isset($template_details['data'])) {
                        // debug($template_details['data'][0]['components']);
                        foreach ($template_details['data'][0]['components'] as $key => $val) {
                            switch ($val['type']) {
                                case "HEADER":
                                    // debug($val);
                                    if ($val['format'] == "text") {
                                        $theader = "<b>" . $val['text'] . "</b>";
                                        // debug($val);
                                    } elseif ($val['format'] == "IMAGE") {
                                        // debug($val);
                                        $theader = '<img class="responsive-image" src="' . $val['example']['header_handle'][0] . '">';
                                    }
                                    break;
                                case "BODY":
                                    $tbody = $val['text'];
                                    break;
                                case "BUTTONS":
                                    foreach ($val['buttons'] as $bkey => $bval) {
                                        $tbutton .= "<button>" . $bval['text'] . "</button>";
                                    }
                                    break;
                            }
                        }
                    }
    
                    if (isset($send_array['template']['components'][0]['parameters'])) {
                        foreach ($send_array['template']['components'][0]['parameters'] as $key => $val) {
                            $key = $key + 1;
                            if ($val['type'] == "text") {
                                $tbody = str_replace('{{' . $key . '}}', $val['text'], $tbody);
                            }
                            if ($val['type'] == "image") {
                                $tbody = str_replace('{{' . $key . '}}', '<div class="responsive-image""><img src="/campaigns/viewsendFile?fileid=' . $val['image']['id'] . '&id=' . $data['stream_id'] . '"></div>', $tbody);
                            }
                        }
                    }
                    $tbody = str_replace('\n', '<br>', $tbody);
                    $tbody = preg_replace('/(?:\*)([^*]*)(?:\*)/', '<strong>$1</strong>', $tbody);
                    $tbody = preg_replace('/(?:_)([^_]*)(?:_)/', '<i>$1</i>', $tbody);
                    $tbody = preg_replace('/(?:~)([^~]*)(?:~)/', '<strike>$1</strike>', $tbody);
                    $msg = $theader . "<br>" . $tbody . "<br>" . $tbutton;
                } else {
                    $msg = "Missing template $template_name";
                }
                break;
            case "text":
                $msg = $send_array['text']['body'];
                break;
            case "interactive":
             //   debug($send_array);
                if(isset($send_array['interactive']['body']['text'])){
                    $msg = "Interactive " . $send_array['interactive']['body']['text'];
                }else{
                    $msg ="Interactive Menu"; 
                }
               
                break;
            case "request_welcome":
                $msg = "requestWelcome";   
                break;
            case "image":
               // debug($send_array);
                $msg= "<figure>";
                $msg =$msg.'<div class="image-container"><img  src="/campaigns/viewsendFile?fileid=' . $send_array['image']['id'] .'&id=' . $data['stream_id'] . '" class="responsive-image" ></div>'; 
                if(isset($send_array['image']['caption'])){
                    $msg =$msg ."<figcaption>".$send_array['image']['caption']."</figcaption>";
                }
                
                $msg =$msg ."</figure>";
                break;    
            case "document":
                   // debug($send_array);
                     $msg ='<a href="/campaigns/viewsendFile?fileid=' . $send_array['document']['id'] . '&id=' . $data['stream_id'] . '" download><i class="material-icons">Download File:' . $send_array['document']['id'] . '</i> </a>';
                    break;   
             case "audio":
                $msg = $msg . '<source src="/campaigns/viewsendFile?fileid=' . $send_array['audio']['id'] . '&id=' . $data['stream_id'] . '" type="audio/mpeg">';
                $msg = $msg . 'Your browser does not support the audio element.</audio></div>';
                $msg = $msg . '<div class="play-button"> <button id="playButton" onclick="togglePlayback()"></button> </div> </div>';
                break;   
            case "video":
                
                $msg= "<figure>";
                $msg = $msg . '<video controls> <source  src="/campaigns/viewsendFile?fileid=' . $send_array['video']['id'] .  '&id=' . $data['stream_id'] . '"> </video>';
                if(isset($send_array['image']['caption'])){
                    $msg =$msg ."<figcaption>".$send_array['image']['caption']."</figcaption>";
                }
                    break;           
            case "reaction":
                $msg = $send_array['reaction']['emoji'];
                    break;               
            default:
                debug($send_array);
        }    
        return $msg;
    }




    
    function _removeTrailingCommas($json) {
        // Remove trailing commas before closing brackets
        $json = preg_replace('/,\s*([\]}])/m', '$1', $json);
        return $json;
    }
}
