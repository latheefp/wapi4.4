<?php if (isset($result['data'])) { ?>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-6 control-label">Excel Fields</label>
        <label for="inputEmail3" class="col-sm-4 control-label">Contact list mapping</label>
    </div>


    <input type="hidden" name="fname" value="<?= $result['fname'] ?>">
    <?php
    foreach ($result['data'] as $key => $val) {
        ?>
       
        <div class="input-group  mt-2">

            <div class="col-md-6 col-sm-6">
                <select class="form-control input-group col-md-12" multiple="" disabled="" >
                    <?php foreach ($val as $sekey => $selval) { ?>
                        <option><?= $selval ?> </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4 col-sm-4">
                <select class="form-control input-group col-md-12" style="width:100%" id="<?= $key ?>" name="<?= $key ?>">
                    <option value="">Don't import</option>
                    <option value="mobile_number">Mobile Number</option>
                    <option value="name">Name</option>
                    <option value="gender">Gender</option>
                    <option value="expiry">Expiry</option>
                    <option value="blocked">Blocked</option>
                </select>
            </div>

        </div>
        <?php
    }
}
