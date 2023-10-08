<?php
echo debug ($messages);
foreach ($messages as $key =>$val){
    debug ($val->message_txt_body);
}