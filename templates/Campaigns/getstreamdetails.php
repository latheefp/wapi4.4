<?php
$session = $this->request->getSession();
$ugroup_id = $session->read('Auth.User.ugroup_id');
//debug($data);
?>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link " data-toggle="tab" href="#tab1-<?= $data->id ?>">Info</a>
    </li>
    <?php if ($ugroup_id == 1) { ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab2-<?= $data->id ?>">Receive Array</a>
        </li>
    <?php } ?>

    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab3-<?= $data->id ?>">Send Data</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab4-<?= $data->id ?>">Result</a>
    </li>
    <?php if ($ugroup_id == 1) { ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab5-<?= $data->id ?>">API Payload</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-json-<?= $data->id ?>"">Json</a>
        </li>


    <?php } ?>
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#tab7-<?= $data->id ?>">Receive Message</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#update-<?= $data->id ?>">Update</a>
    </li>


</ul>

<div class="tab-content col-md-9">
    <div id="tab1-<?= $data->id ?>" class="tab-pane fade ">
        <table>
            <tbody>
                <tr><th>Initiator</th><td><?= $data->initiator ?></td></tr>
                <tr><th>Context</th><td><?= $data->message_context ?></td></tr>
                <tr><th>Format Type</th><td><?= $data->message_format_type ?></td></tr>
                <tr><th>Success</th><td><?= $data->success ?></td></tr>
                <tr><th>Format Type</th><td><?= $data->message_format_type ?></td></tr>
            </tbody>
        </table>
    </div>
    <?php if ($ugroup_id == 1) { ?>
        <div id="tab2-<?= $data->id ?>" class="tab-pane fade">
            <table>
                <tbody>
                    <tr><td><pre>
                                <?php
                                $decoded_object = json_decode($data->recievearray, true);
                                $beautified_json_string = json_encode($decoded_object, JSON_PRETTY_PRINT);
                                echo ($beautified_json_string);
                                ?>
                            </pre>
                        </td></tr>

                </tbody>
            </table>
        </div>

        <div id="tab-json-<?= $data->id ?>" class="tab-pane fade">
            <table>
                <tbody>
                    <tr><td><pre>
                                <?php
                                $json = trim($data->tmp_upate_json, ',');
                                $jsonArray = explode("\n", $json);
                                foreach ($jsonArray as $jkey => $jval) {
                                    if (!empty($jval)) {
                                        //    debug($jval);
                                        $jval = trim($jval, ',');
                                        $status = json_decode($jval, true);
                                        print_r($status);
                                    }
                                }
                                ?>
                            </pre>
                        </td></tr>
                </tbody>
            </table>
        </div>

    <?php } ?>

    <div id="tab3-<?= $data->id ?>" class="tab-pane fade">
        <table>
            <tbody>
                <tr><td><pre>
                            <?php
                            $decoded_object = json_decode($data->sendarray, true);
                            $beautified_json_string = json_encode($decoded_object, JSON_PRETTY_PRINT);
                            echo ($beautified_json_string);
                            ?>
                        </pre>
                    </td></tr>
            </tbody>
        </table>
    </div>
    <div id="tab4-<?= $data->id ?>" class="tab-pane fade">
        <table>
            <tbody>
                <tr><td><pre>
                            <?php
                            $decoded_object = json_decode($data->result, true);
                            $beautified_json_string = json_encode($decoded_object, JSON_PRETTY_PRINT);
                            echo ($beautified_json_string);
                            ?>
                        </pre>
                    </td></tr>
            </tbody>
        </table>
    </div>
    <div id="tab5-<?= $data->id ?>" class="tab-pane fade">
        <table>
            <tbody>
                <tr><td><pre>
                            <?php
                            $decoded_object = json_decode($data->postdata, true);
                            $beautified_json_string = json_encode($decoded_object, JSON_PRETTY_PRINT);
                            echo ($beautified_json_string);
                            ?>
                        </pre>
                    </td></tr>
            </tbody>
        </table>
    </div>

<!--    <div id="tab6-<?= $data->id ?>" class="tab-pane fade show active">
    <table>
        <tbody>
            <tr><td><pre>
    <?php
    $rcvData['json'] = $data->recievearray;
    $rcvData['id'] = $data->id;
    $rcvData['FBsettings'] = $FBsettings;
    print($this->RcvDataformat->format($rcvData));
    ?>
                    </pre>
                </td></tr>
        </tbody>
    </table>
</div>-->



    <div id="tab7-<?= $data->id ?>" class="tab-pane fade show active">
        <table>
            <tbody>
                <tr><td><pre>
                            <?php
                            $rcvData['json'] = $data->recievearray;
                            $rcvData['FBsettings'] = $FBsettings;
                            print($this->RcvDataformat->format($rcvData));
                            ?>
                        </pre>
                    </td></tr>
            </tbody>
        </table>
    </div>
    <div id="update-<?= $data->id ?>" class="tab-pane fade">

        <div class="comment-section col-md-8 comment-section-<?= $data->id ?>">
            <?php
            foreach ($updates as $key => $val) {
                // debug($val);
                ?>
                <div class="comment">
                    <div class="comment-header">
                        <span class="username"><?= $val->user->name ?></span>
                        <span class="timestamp"><?= $this->Time->timeAgoInWords($val->created); ?></span>
                    </div>
                    <div class="comment-content">
                        <?= $val->comment ?>
                    </div>
                </div>

                <?php
            }
            ?>
        </div>
        <form id="updateForm<?= $data->id ?>" class="mt-3">
            <div class="form-group">
                <label for="updateText<?= $data->id ?>">Update Text</label>
                <textarea class="form-control col-md-8" id="updateText<?= $data->id ?>" name="updateText" rows="4" placeholder="Enter update text"></textarea>            </div>
            <button type="button" class="btn btn-primary" onclick="sendUpdate(<?= $data->id ?>)">Submit Update</button>
            <button type="button" class="btn btn-danger" onclick="blocknumber(<?= $data->id ?>)">Block Sending</button>
        </form>
    </div>



</div>


<style><!-- comment -->
    .comment {
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        background-color: lightblue;
        font-weight: bold;
        font-style: italic;
        padding: 5px 10px;
    }

    .comment-header .username {
        flex-grow: 1;
    }

    .comment-header .timestamp {
        text-align: right;
        flex-shrink: 0;
    }

</style>