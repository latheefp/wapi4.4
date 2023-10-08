

<?php

//debug($groups);
foreach ($groups as $key => $val) {
  //   debug($val);
    ?>
    <li class="nav-item active " >
        <a href="#" class="nav-link " id="<?= $val->id ?>">
            <i class="fas fa-users  load_numbers"></i> <?= $val->name ?>
            <div class="float-right">
                <span class="badge bg-primary"><?= $val->contact_count ?></span>
                <span class="badge bg-danger" type="delete" id="<?= $val->id ?>"> <i type="delete" id="<?= $val->id ?>"  gname="<?= $val->name ?>" class="fas fa-trash-alt"></i> </span>
            </div>
        </a>
    </li>
    <?php
} 