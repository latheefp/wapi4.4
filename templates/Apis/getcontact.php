<ul>
<?php
foreach ($contacts as $key =>$val){?>
     <li class="contact" contact="<?= $val->contact_wa_id ?>" onclick="loadchat(<?= $val->contact_wa_id ?>)">
        <div class="wrap">
            <span class="contact-status online"></span>
            <img src="http://emilcarlsson.se/assets/louislitt.png" alt="" />
            <div class="meta">
                <p class="name"><?= $val->contact_wa_id ?></p>
                <p class="preview"><?= $val->contact_wa_id ?>.</p>
            </div>
        </div>
    </li>
<?php  }?>
</ul>
    