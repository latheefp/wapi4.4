<?php
$success = $total - $failed;
$success_per = round($success / $total * 100,1);
$failed_per = round($failed / $total * 100,1);
$formarray = [];
foreach ($formdata as $key => $val) {
    $formarray[$val['field_name']] = $val;
}

//debug ($formarray);
?>
<div class="row">
    <div class="col-md-3">
        <div class="progress-group">
            <span class="progress-text">Delivery Status</span>
            <span class="progress-number"><b><?= $success ?>/<?= $total ?></b></span>
            <div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $success_per ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"> Success: <?= $success_per ?>%</div>
                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $failed_per ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"> No WhatsApp: <?= $failed_per ?>%</div>
            </div>

        </div>
    </div>
    <div class="col-md-9 ">
        <div style="display:flex; justify-content:flex-end; width:100%; padding:0;">
            <a type="button" class="btn btn-info btn-sm" href="/campaigns/schedulereport/<?= $id ?>" value="Details">Details</a>
        </div>
    </div>

</div>

<div class="col-md-12">
    <!--        <div class="card card-primary">
                <div class="card-body">-->
    <?php
    $json_array = (json_decode($data['template_details'], true));

    $count = count($json_array['data']);
    foreach ($json_array['data'] as $dkey => $dval) {
        $lang = $dval['language'];
        $status = $dval['status'];
        $category = $dval['category'];
        ?>
        <!--<div class="info-box-content">-->
        <div >
            <div class="row">
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header">LANGUAGE</h5>
                        <span class="description-text"><?= $lang ?></span>
                    </div>

                </div>

                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header">STATUS</h5>
                        <span class="description-text"><?= $status ?></span>
                    </div>

                </div>

                <div class="col-sm-4">
                    <div class="description-block">
                        <h5 class="description-header">CATEGORY</h5>
                        <span class="description-text"><?= $category ?></span>
                    </div>

                </div>

            </div>

        </div>
        <div class = "card-body  whatsappbg" style="white-space:normal">

            <?php
            foreach ($dval as $cdkey => $cdval) {
                if ($cdkey == "components") {
                    foreach ($cdval as $key => $val) {
                        switch ($val['type']) {
                            case "HEADER":
                                switch ($val['format']) {
                                    case "TEXT":
                                        ?>
                                        <strong> <?= $val['text'] ?></strong><br>
                                        <?php
                                        break;
                                    case "IMAGE":
                                        $fname = "file-" . $lang . "-header-image";
//                                                               debug ($fname);
//                                                               debug ($formarray);
                                        ?>
                                        <div>

                                            <div class="col-sm-2">
                                                <?php
                                                ?>
                                                <a href="/campaigns/viewsendFile?fileid=<?= $formarray[$fname]['fbimageid'] . "/" . $formarray[$fname]['field_value'] ?>" data-toggle="lightbox"  data-gallery="gallery">
                                                    <?php if (isset($formarray[$fname])) {
                                                        ?>
                                                        <img  class="img-fluid mb-2" src="/campaigns/viewsendFile?fileid=<?= $formarray[$fname]['fbimageid'] ?>"  id="<?= $fname ?>-prev">
                                                    <?php } else {
                                                        ?>
                                                        <img id="<?= $fname ?>-prev src="">
                                                    <?php }
                                                    ?>
                                                </a>
                                            </div>
                                        </div>


                                        <?php
                                        break;
                                    case "MEDIA":
                                        $fname = "file-" . $lang . "-header-media";
                                        ?>
                                        <div>
                                            <?php if (isset($formarray[$fname])) {
                                                ?>
                                                <img src="https://wa.egrand.in/campaigns/viewimage/"<?= $formarray[$fname]['fbimageid'] ?> id="<?= $fname ?>-prev>
                                            <?php } else {
                                                ?>
                                                     <img id="<?= $fname ?>-prev" src="">
                                                 <?php }
                                                 ?>
                                        </div>
                                        <?php
                                        break;
                                    case "DOCUMENT":
                                        $fname = "file-" . $lang . "-header-document";
                                        ?>
                                        <div>
                                            <?php if (isset($formarray[$fname])) {
                                                ?>
                                                <img src="/campaigns/viewimage/"<?= $formarray[$fname]['fbimageid'] ?> id="<?= $fname ?>-prev>
                                            <?php } else {
                                                ?>
                                                     <img id="<?= $fname ?>-prev" src="">
                                                 <?php }
                                                 ?>
                                        </div>
                                        <?php
                                        break;
                                    case "VIDEO":
                                        $fname = "file-" . $lang . "-header-video";
                                        ?>
                                        <div>
                                            <?php if (isset($formarray[$fname])) {
                                                ?>
                                                <img src="/campaigns/viewimage/"<?= $formarray[$fname]['id'] ?> id="<?= $fname ?>-prev>
                                            <?php } else {
                                                ?>
                                                     <img id="<?= $fname ?>-prev" src="">
                                                 <?php }
                                                 ?>
                                        </div>
                                        <?php
                                        break;
                                    default:
                                        debug($val);
                                        break;
                                }
                                break;
                            case "BODY":
                                ?>
<!--                                <div class="card-body col-md-6">-->
                                    <?php
                                    $text = $val['text'];
                                    for ($i = 1; $i <= 32; $i++) {
                                        $text = str_replace('{{' . $i . '}}', '<i id="var-' . $i . "-" . $lang . '"></i>', $text);
                                    }
                                    print $text . '<br>';
                                    ?>
                                <!--</div>-->
                            </div>
                            <?php
                            break;
                        case "FOOTER":
                            ?>
                            <small>
                            <?= $val['text'] ?></small><br>
                            <?php
                            break;
                        case "BUTTONS":
                            ?>
                            <div class="form-group">
                                <?php
                                foreach ($val['buttons'] as $bkey => $bval) {
                                    if ($bval['type'] == "QUICK_REPLY") {
                                        ?>
                                        <button class="btn btn-info">
                                            <?= $bval['text'] ?></button>
                                            <?php
                                        }
                                    }
                                    ?>
                            </div>
                            <?php
                            break;
                    }
                }
            }
        }
        ?>

    </div>
    <!--</div>-->
    </div>



    <?php
}
?>
<!--            </div>
        </div>-->

<!--    </div>-->









