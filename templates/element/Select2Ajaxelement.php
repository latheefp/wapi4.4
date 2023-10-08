
<div class="form-group">
    <select class="form-control select2 "  multiple="multiple"  maxlength="12"  minlength="12"  required="" name="<?= $name ?>" id="<?= $id ?>"  tabindex="-1" aria-hidden="true">
         <option value='0'>- Search Value -</option>
    </select>
</div>
<?php $this->Html->scriptStart(['block' => true]); ?>
    $(document).ready(function () {

        $("#<?= $id ?>").select2({
            //     console.log("Calling Ajax");
            ajax: {
                url: "/ajaxes/getselect2data/<?= $token ?>",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term // search term
                    };
                },
                beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    });
<?php $this->Html->scriptEnd(); ?>