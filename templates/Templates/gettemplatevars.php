        
<?php
$json_array = (json_decode($data['template_details'], true));
$count = count($json_array['data']);
foreach ($json_array['data'] as $dkey => $dval) {
    $lang = $dval['language'];
    $status = $dval['status'];
    $category = $dval['category'];
    foreach ($dval as $cdkey => $cdval) {
        if ($cdkey == "components") {
            foreach ($cdval as $key => $val) {
                switch ($val['type']) {
                    case "HEADER":
                        switch ($val['format']) {

                            case "IMAGE":
                                ?>
                                <div class="form-group">
                                    <label for="exampleInputFile">Select Image for <?= $lang ?> Language </label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file-<?= $lang ?>-headerfile" name="file-<?= $lang ?>-headerfile">
                                            <label class="custom-file-label" for="customFile">Choose Image</label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                break;
                            case "MEDIA":
                                ?>
                               <div class="form-group">
                                    <label for="exampleInputFile">Select Media for <?= $lang ?> Language </label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file-<?= $lang ?>-headerfile" name="file-<?= $lang ?>-headerfile">
                                            <label class="custom-file-label" for="customFile">Choose Media</label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                break;
                            case "DOCUMENT":
                                ?>
                                <div class="form-group">
                                    <label for="exampleInputFile">Select Document for <?= $lang ?> Language </label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file-<?= $lang ?>-headerfile" name="file-<?= $lang ?>-headerfile">
                                            <label class="custom-file-label" for="customFile">Choose Document</label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                break;
                            case "VIDEO":
                                ?>
                                <div class="form-group">
                                    <label for="exampleInputFile">Select Video for <?= $lang ?> Language </label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file-<?= $lang ?>-headerfile" name="file-<?= $lang ?>-headerfile">
                                            <label class="custom-file-label" for="customFile">Choose Video</label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                break;
                            default:
                                debug($val);
                                break;
                        }
                        break;
                    case "BODY":
                        $text = $val['text'];
                        for ($i = 1; $i <= 32; $i++) {
                            $match = substr_count($text, '{{' . $i . '}}');
                            if ($match == 1) {
                                ?>
                                <div class="form-group col-sm-6">
                                    <label>Variable <?= $i ?>:</label>
                                    <input type="text" class="form-control input-group-lg" name="[<?= $lang ?>]<?= $lang ?>var-<?= $i ?>" id="<?= $lang ?>.var-<?= $i ?>" required="" placeholder="Variable <?= $i ?>">
                                </div>
                                <?php
                            }
                        }

                    case "FOOTER":

                        break;
                    case "BUTTONS":

                        break;
                }
            }
        }
    }
}
?>


