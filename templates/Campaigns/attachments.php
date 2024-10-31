<?php
//debug($data);
$formarray = [];
foreach ($formdata as $key => $val) {
    $formarray[$val['field_name']] = $val;
}
$varcount = 1;
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Campaign info</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div tabindex="1" class="col col-xl-3">
                        <label>Campaign Name *</label>
                        <input type="text" readonly class="form-control" name="campaign_name" id="campaign_name" required="" value="<?= $camp->campaign_name ?>" placeholder="Campaign Name">
                    </div>
                    <div tabindex="1" class="col col-xl-3">
                        <label>Start Date *</label>
                        <input type="text" readonly class="form-control" name="start_date" id="start_date" value="<?= $camp->start_date ?>" required="" placeholder="Start Date">
                    </div>
                    <div tabindex="1" class="col col-xl-3">
                        <label>Template Name *</label>
                        <input type="text" readonly class="form-control" name="template_name" id="template_name" value="<?= $camp->template_id ?>" required="" placeholder="Template Name">
                    </div>
                    <div tabindex="2" class="col col-xl-3">
                        <label>End Date *</label>
                        <div>
                            <input type="text" readonly class="form-control" name="end_date" id="end_date" value="<?= $camp->end_date ?>" required="" placeholder="End Date">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Template Attachment and Variables</h3>
            </div>

            <div class="card-body">
                <?php
                $products = null;
                echo $this->Form->create(
                    $products,
                    [
                        'type' => 'post',
                        'url' => '/campaigns/attachments',
                        'idPrefix' => 'newcontactlist',
                        'id' => 'newcampform',
                        'defaction' => null,
                        'class' => ["form-horizontal", "needs-validation"],
                        "novalidate",
                        'enctype' => 'multipart/form-data'
                    ]
                );

                $json_array = (json_decode($data['template_details'], true));
                //    debug($json_array);
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


                                            case "TEXT":
                                                $head = $val['text'];
                                                for ($i = $varcount; $i <= 32; $i++) {
                                                    $match = substr_count($head, '{{' . $i . '}}');
                                                    if ($match == 1) {
                                                        $var = 'var-' . $i . '-' . $lang;
                                                        if (isset($formarray[$var])) {
                                                            $cvar = $formarray[$var]['field_value'];
                                                        } else {
                                                            $cvar = null;
                                                        }
                ?>
                                                        <div class="form-group col-sm-6">
                                                            <label>Header Variable <?= $i ?> of <?= $lang ?>:</label>
                                                            <input type="text" class="form-control input-group-lg whatsappvar" name="//<?= 'var-' . $i . "-" . $lang ?>" value="" <?= $cvar ?>" required="" placeholder="Variable <?= $i ?>">
                                                        </div>
                                                <?php
                                                        $varcount = $varcount + 1;
                                                    }
                                                }


                                                break;

                                            case "IMAGE":
                                                $fname = "file-" . $lang . "-header-image";
                                                ?>
                                                <div class="form-group">
                                                    <label for="exampleInputFile">Select Image for <?= $lang ?> Language </label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input loadimage" id="<?= $fname ?>" name="<?= $fname ?>" accept=".jpg">
                                                            <label class="custom-file-label" for="customFile">Choose Image</label>

                                                        </div>

                                                    </div>
                                                </div>
                                            <?php
                                                break;
                                            case "MEDIA":
                                                $fname = "file-" . $lang . "-header-media";
                                            ?>
                                                <div class="form-group">
                                                    <label for="exampleInputFile">Select Media for <?= $lang ?> Language </label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input loadimage" id="<?= $fname ?>" name="<?= $fname ?>" accept=".mp4">
                                                            <label class="custom-file-label" for="customFile">Choose Media</label>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php
                                                break;
                                            case "DOCUMENT":
                                                $fname = "file-" . $lang . "-header-document";
                                            ?>
                                                <div class="form-group">
                                                    <label for="exampleInputFile">Select Document for <?= $lang ?> Language </label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input loadimage" id="<?= $fname ?>" name="<?= $fname ?>" accept=".pdf">
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
                                                            <input type="file" class="custom-file-input" id="file-<?= $lang ?>-header-video" name="file-<?= $lang ?>-header-video">
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
                                        //  debug($varcount);
                                        for ($i = $varcount; $i <= 32; $i++) {
                                            $match = substr_count($text, '{{' . $i . '}}');
                                            if ($match == 1) {
                                                $var = 'var-' . $i . '-' . $lang;
                                                if (isset($formarray[$var])) {
                                                    $cvar = $formarray[$var]['field_value'];
                                                    //   debug($cvar);
                                                } else {
                                                    $cvar = null;
                                                }
                                            ?>
                                                <div class="form-group col-sm-6">
                                                    <label>Body Variable <?= $i ?> of <?= $lang ?>:</label>
                                                    <input type="text" class="form-control input-group-lg whatsappvar" name="<?= 'var-' . $i . "-" . $lang ?>" value="<?= $cvar ?>" required="" placeholder="Variable <?= $i ?>">
                                                </div>
                                            <?php
                                                $varcount++;
                                            }
                                        }



                                    case "FOOTER":

                                        break;
                                    case "BUTTONS":
                                        //if button has a example member, which means, there a variable.
                                        foreach ($val['buttons'] as $bkey => $bval) {
                                            //             debug($bkey);
                                            if (isset($bval['example'])) {
                                                $var = "button-$bkey-" . $lang;
                                                if (isset($formarray[$var])) {
                                                    $cvar = $formarray[$var]['field_value'];
                                                } else {
                                                    $cvar = null;
                                                }
                                            ?>
                                                <div class="form-group col-sm-6">
                                                    <label>Button Variable of <?= $lang ?>:</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control input-group-lg whatsappvar" name="<?= "button-$bkey-$lang" ?>" value="<?= $cvar ?>" required="" placeholder="Button Variable">

                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="auto_inject" value="1" id="auto_inject" <?= $camp->auto_inject == 1 ? 'checked' : '' ?>> Enable tracking Auto inject with custom encoded JSON. 
                                                        </label>
                                                        <div id="injectContainer" style="display: none;">
                                                            <textarea class="col-md-12" id="jsonTextArea" rows="10" cols="50" name="inject_text"><?= $camp->inject_text ?></textarea>
                                                            <div class="input-group-append">
                                                                <!-- Change input to textarea for multiline placeholder -->
                                                                <textarea class="form-control input-group-lg" readonly placeholder="You can replace the value with known fields. Currently it supports only {{mobile}}"></textarea>
                                                            </div>
                                                            <div class="link-container" style="display: flex;  justify-content: space-between;">
                                                                <a href="#" class="link-primary" onclick="beautifyJson(event)">Beautify JSON</a>
                                                                <a href="#" class="link-primary" onclick="loadsample(event)">Load Sample JSON</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                <?php
                                            }
                                        }

                                        break;
                                }
                            }
                        }
                    }
                }
                ?>
                <div class="card-footer">
                    <button type="submit" id="submitform" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-info float-right">Cancel</button>
                </div>
                <input type="text" readonly class="form-control" name="id" id="id" value="<?= $camp->id ?>" hidden placeholder="End Date">
                <?php echo $this->Form->end() ?>
            </div>


        </div>
    </div>


    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Message Preview</h3>
            </div>

            <div class="card-body">
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
                            <div class="card-body">

                                <?php
                                foreach ($dval as $cdkey => $cdval) {
                                    if ($cdkey == "components") {
                                        foreach ($cdval as $key => $val) {

                                            switch ($val['type']) {

                                                case "HEADER":
                                                    switch ($val['format']) {
                                                        case "TEXT":
                                ?>
                                                            <strong>
                                                                <?php
                                                                $text = $val['text'];
                                                                for ($i = 1; $i <= 32; $i++) {
                                                                    $var = 'var-' . $i . '-' . $lang;
                                                                    if (isset($formarray[$var])) {
                                                                        $cvar = $formarray[$var]['field_value'];
                                                                    } else {
                                                                        $cvar = 'VAR-' . $i;
                                                                    }
                                                                    $text = str_replace('{{' . $i . '}}', '<i id="var-' . $i . "-" . $lang . '">' . $cvar . '</i>', $text);
                                                                }
                                                                print $text . '<br>';
                                                                ?>

                                                            </strong><br>
                                                        <?php
                                                            break;
                                                        case "IMAGE":
                                                            //   debug($val);
                                                            $fname = "file-" . $lang . "-header-image";
                                                            // debug($fname);
                                                            // debug($formarray);
                                                        ?>
                                                            <div>

                                                                <div class="col-sm-4">
                                                                    <?php if (isset($formarray[$fname])) {
                                                                    ?>
                                                                        <a href="/campaigns/viewsendFile?fileid=<?= $formarray[$fname]['fbimageid'] . "/" . $data['account_id'] . "/" . $formarray[$fname]['field_value'] ?>" data-toggle="lightbox" data-gallery="gallery">
                                                                            <img class="img-fluid mb-2" src="/campaigns/viewsendFile?fileid=<?= $formarray[$fname]['fbimageid'] ?>" id="<?= $fname ?>-prev">
                                                                        <?php } else {
                                                                        ?>
                                                                            <img id="<?= $fname ?>-prev" src="">
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
                                                                    <img src="/campaigns/viewsendFile?fileid=" <?= $formarray[$fname]['fbimageid'] ?> id="<?= $fname ?>-prev>
                                                                <?php } else {
                                                                ?>
                                                                         <img id=" <?= $fname ?>-prev" src="">
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
                                                                    <img src="/campaigns/viewsendFile?fileid=" <?= $formarray[$fname]['fbimageid'] ?> id="<?= $fname ?>-prev>
                                                                <?php } else {
                                                                ?>
                                                                         <img id=" <?= $fname ?>-prev" src="">
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
                                                                    <img src="/campaigns/viewsendFile?fileid=" <?= $formarray[$fname]['id'] ?> id="<?= $fname ?>-prev>
                                                                <?php } else {
                                                                ?>
                                                                         <img id=" <?= $fname ?>-prev" src="">
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
                                                    $text = $val['text'];
                                                    for ($i = 1; $i <= 32; $i++) {
                                                        $var = 'var-' . $i . '-' . $lang;
                                                        if (isset($formarray[$var])) {
                                                            $cvar = $formarray[$var]['field_value'];
                                                        } else {
                                                            $cvar = 'VAR-' . $i;
                                                        }
                                                        $text = str_replace('{{' . $i . '}}', '<i id="var-' . $i . "-" . $lang . '">' . $cvar . '</i>', $text);
                                                    }
                                                    print $text . '<br>';
                                                    break;
                                                case "FOOTER":
                                                    ?>
                                                    <small>
                                                        <?= $val['text'] ?></small><br>
                                                <?php
                                                    break;
                                                case "BUTTONS":

                                                    //  debug($val['buttons']);
                                                ?>
                                                    <div class="button-container">
                                                        <?php
                                                        foreach ($val['buttons'] as $bkey => $bval) {
                                                            switch ($bval['type']) {
                                                                case 'PHONE_NUMBER': ?>
                                                                    <div class="form-group">
                                                                        <button><?= $bval['text'] . "=>" . $bval['phone_number'] ?></button>
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'URL':
                                                                ?>
                                                                    <div class="form-group">
                                                                        <button><?= $bval['text'] . "=>" . $bval['url'] ?></button>
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                default:
                                                                ?>
                                                                    <div class="form-group">
                                                                        <button><?= $bval['text'] ?></button>
                                                                    </div>
                                                        <?php
                                                                    break;
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
                        </div>
                    </div>



                <?php
                }
                ?>
            </div>
        </div>

    </div>

</div>



<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
    $(function() {

        $(".content").on("change", ".custom-file-input", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        $('#template_id').on('select2:selecting', function(e) {
            id = e.params.args.data.id;
            $.ajax({
                beforeSend: function(xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                type: "POST",
                url: '/templates/gettemplatejson/' + id,
                success: function(data) {
                    $('#msgbox').html(data);
                }
            });

            $.ajax({
                beforeSend: function(xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                type: "POST",
                url: '/templates/gettemplatevars/' + id,
                success: function(data) {
                    $('#variables').html(data);
                }
            });
        })

        $('.whatsappvar').on('keyup', function() {
            var name = this.name
            $('#' + name).html(this.value);
            console.log("setting " + this.value + "on" + name)
        })

        $('.loadimage').on('change', function() {
            var name = this.name
            var id = this.id;
            console.log(name + "," + id)
            var files = $(this)[0].files;
            if (files.length > 0) {
                var src = URL.createObjectURL(this.files[0]);
                var preview = document.getElementById(id + "-prev");
                preview.width = 500;
                preview.src = src;
                preview.style.display = "block";
            }




        })
    }) //end of DR


    $('#newcamp-btn').click(function(event) {
        //  alert("you are submitting form.")
        //            document.getElementById("submitdatabutton").hidden = true;
        //            document.getElementById("submitspin").hidden = false;

        event.preventDefault();
        var form = $("#newcampform");
        var form2 = $('#varform');
        data = form.serialize();
        data2 = form2.serialize();

        formData = new FormData(),
            params = form.serializeArray(),
            params2 = form2.serializeArray(),
            file = form2.find('input[type="file"]')[0].files;; //
        //var form = $("#newcampform");
        var ins = form2.find('input[type="file"]').length;
        for ($i = 0; $i < ins; $i++) {
            $fname = form2.find('input[type="file"]')[$i]['name'];
            formData.append('file[' + $fname + ']', form2.find('input[type="file"]')[$i].files[0]);
        }



        $.each(params, function(i, val) {
            formData.append('[campaign]' + val.name, val.value);
        });
        $.each(params2, function(i, val) {
            formData.append('[campaign]' + val.name, val.value);
        });
        // formData.append('id', Math.random());

        console.log(formData);
        $.ajax({
                url: "/campaigns/newcamp",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function(xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                }
            })
            .done(function(data) {
                var jsonData = JSON.parse(data);
                status = jsonData.status;
                msg = jsonData.msg;
                if (status == "success") {
                    toastr['success'](msg);
                    $('#groupaddbutton').show();
                    $('#contactaddinputdiv').hide();
                    loadcontactlist();
                } else {
                    toastr['error'](msg);
                }
                document.getElementById("submitdatabutton").hidden = false;
                document.getElementById("submitspin").hidden = true;
            });

    })

    function showPreview(event) {
        if (event.target.files.length > 0) {
            var src = URL.createObjectURL(event.target.files[0]);
            //            console.log(this.name);
            //            console.log(this.id);
            var preview = document.getElementById("file-ip-1-preview");
            preview.src = src;
            preview.style.display = "block";
        }
    }

    function beautifyJson() {
        const jsonTextArea = document.getElementById("jsonTextArea");
        try {
            const parsedJson = JSON.parse(jsonTextArea.value);
            const beautifiedJson = JSON.stringify(parsedJson, null, 4); // 4 is the number of spaces for indentation
            jsonTextArea.value = beautifiedJson;
        } catch (error) {
            alert("Invalid JSON");
        }
    }

    //  loadsample(event)

    function loadsample() {
        const jsonTextArea = document.getElementById("jsonTextArea");
        var sample = '{"issue":"Ac Service","mobile":"##mobile##","campaign_id":56,"service_type_id":14,"account_id":1,"action":"camps","backend":true}'
        try {
            const parsedJson = JSON.parse(sample);
            const beautifiedJson = JSON.stringify(parsedJson, null, 4); // 4 is the number of spaces for indentation
            jsonTextArea.value = beautifiedJson;
        } catch (error) {
            alert("Invalid JSON");
        }
    }


    function toggleInjectContainer() {
        var checkbox = document.getElementById('auto_inject');
        var injectContainer = document.getElementById('injectContainer');
        if (checkbox.checked) {
            injectContainer.style.display = 'block';
        } else {
            injectContainer.style.display = 'none';
        }
    }

    // Add event listener to checkbox
    document.getElementById('auto_inject').addEventListener('change', toggleInjectContainer);

    // Call the function initially to set initial state
    toggleInjectContainer();

    //
</script>
<?php $this->Html->scriptEnd(); ?>