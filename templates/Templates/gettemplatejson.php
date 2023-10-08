<div class = "card card-primary card-outline direct-chat direct-chat-primary shadow-none">
    <div class = "card-header">
        <h3 class = "card-title">Message Template Details:</h3>
        <div class = "card-tools">
            <button type = "button" class = "btn btn-tool" title = "Contacts" data-widget = "chat-pane-toggle">
                <i class = "fas fa-comments"></i>
            </button>
        </div>
    </div>
    <?php
    $json_array = (json_decode($data['template_details'], true));
    $count = count($json_array['data']);
    foreach ($json_array['data'] as $dkey => $dval) {
        $lang = $dval['language'];
        $status = $dval['status'];
        $category = $dval['category'];
        ?>

        <div class="info-box">
            <div class="info-box-content">

                <div class="card-header">
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
                <div class = "card-body">

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
                                                break;
                                            case "MEDIA":
                                                break;
                                            case "DOCUMENT":
                                                break;
                                            case "VIDEO":
                                                break;
                                            default:
                                                debug($val);
                                                break;
                                        }
                                        break;
                                    case "BODY":
                                        $text = $val['text'];
                                        for ($i = 1; $i <= 32; $i++) {
                                            $text = str_replace('{{' . $i . '}}', '<div id="var_'.$i.$lang.'"></div>', $text);
                                        }
                                        print $text;
                                        break;
                                    case "FOOTER":
                                        ?>
                                        <small>
                                            <?= $val['text'] ?></small><br>
                                        <?php
                                        break;
                                    case "BUTTONS":
                                        print '<div class="form-group">';
                                        foreach ($val['buttons'] as $bkey => $bval) {
                                            if ($bval['type'] == "QUICK_REPLY") {
                                                ?>
                                                <button class="btn btn-info">
                                                    <?= $bval['text'] ?></button>
                                                    <?php
                                                }
                                            }
                                            print " </div>";
                                            break;
                                    }
                                }
                            }
                        }
                        ?>

                </div>
            </div>

        </div>

        <?php
    }

//}
    ?>

</div>



