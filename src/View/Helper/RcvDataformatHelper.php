<?php

namespace App\View\Helper;

//use Cake\Controller\Controller;
use Cake\View\Helper;
use Cake\View\View;

class RcvDataformatHelper extends Helper {

    public function initialize(array $config): void {
        //     debug($config);
    }

    function format($data) {
        $rcarray = json_decode($data['json'], true);
        //  debug($rcarray);
        if (!is_array($rcarray)) {
            return false;
        }
        $message_array = $rcarray['entry'][0]['changes'][0]['value']['messages'];
        $result = null;
        //   debug($data);
        foreach ($message_array as $key => $val) {
          //    debug($val);
            $type = $val['type'];
            switch ($type) {
                case "image":
                    $result = $result . '<img src="/campaigns/viewrcvImage?fileid=' . $val['image']['id'] . '&type=' . $val['image']['mime_type'] . '&id=' . $data['id'] . '">';

                    break;
                case "document":
//                    $result = $result . '<img src="/campaigns/viewrcvImage?fileid=' . $val['document']['id'] . '&type=' . $val['document']['mime_type'] . '">';
                    $result = $result . '<a href="/campaigns/viewrcvImage?fileid=' . $val['document']['id'] . '&type=' . $val['document']['mime_type'] . '&id=' . $data['id'] . '" download><i class="material-icons">Download File:'.$val['document']['filename'].'</i> </a>';
                    break;
                case "video":
                    $result = $result . '<video controls> <source  src="/campaigns/viewrcvImage?fileid=' . $val['video']['id'] . '&type=' . $val['video']['mime_type'] . '&id=' . $data['id'] . '"> </video>';

                    break;
                case "text":
                    $result = $result . $val['text']['body'];
                    break;
                case "location":
                    $result = $result . "https://maps.google.com/?q=" . $val['location']['latitude'] . "," . $val['location']['longitude'];
                    break;
                case "sticker":
                    $result = $result . '<img  width="512" height="512" src="/campaigns/viewrcvImage?fileid=' . $val['sticker']['id'] . '&type=' . $val['sticker']['mime_type'] . '&id=' . $data['id'] . '">';
                    break;
                case "interactive":
                    $result = $result . "Interactive Reply:" . $val['interactive']['list_reply']['title'] . ":" . $val['interactive']['list_reply']['description'];
                    break;
                case "audio":
                    $result = $result . '<div class="audio-message"><div class="audio-player"><audio id="audioPlayer" controls>';
                    $result = $result . '<source src="/campaigns/viewrcvImage?fileid=' . $val['audio']['id'] . '&type=' . $val['audio']['mime_type'] . '&id=' . $data['id'] . '" type="audio/mpeg">';
                    $result = $result . 'Your browser does not support the audio element.</audio></div>';
                    $result = $result . '<div class="play-button"> <button id="playButton" onclick="togglePlayback()"></button> </div> </div>';
                    break;
                case "reaction":
                    $result = $result . "Reaction:" . $val['reaction']['emoji'];
                    break;
                case "contacts":
                    $i = 0;
                    $result = $result . ' <div class="container"><div class="table-responsive">';
                    foreach ($val['contacts'] as $ckey => $cval) {
                        $i++;
                        $result = $result . "<h4>Shared Contact: $i </h4><table class='table table-boarderd col-md-6'>";
                        if (isset($cval['name']['first_name'])) {
                            $result = $result . "<tr><td><b>First Name</b></td><td>" . $cval['name']['first_name'] . "</td></tr>";
                        }

                        if (isset($cval['org']['company'])) {
                            $result = $result . "<tr><td><b>Organization</b></td><td>" . $cval['org']['company'] . "</td></tr>";
                        }


                        $result = $result . "<tr><td><b>Last Name</b></td><td>" . $cval['name']['last_name'] . "</td></tr>";
                        $result = $result . "<tr><td><b>Formated Name</b></td><td>" . $cval['name']['formatted_name'] . "</td></tr>";

                        foreach ($cval['phones'] as $key => $val) {
                            $result = $result . "<tr><td><b>" . $val['type'] . "</b></td><td>" . $val['phone'] . "</td></tr>";
                        }
                        $result = $result . "</table>";
                        $result = $result . "<br>";
                    }
                    $result = $result . "</div></div>";

                    break;
            }
            if (isset($val[$type]['caption'])) {
                $result = $result . "<br><div>" . $val[$type]['caption'] . "</div>";
            }
        }

        return $result;
    }

//    function sanitizeString($string) {
//        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
//    }
}
