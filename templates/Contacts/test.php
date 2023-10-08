
<label>Minimal</label>

<select id="selUser" class="form-control " style="width: 100%;">

    <option value='0'>- Search user -</option>
</select>


<?php $this->Html->scriptStart(['block' => true]); ?>
//<script>
    $(document).ready(function () {

        $("#selUser").select2({
       //     console.log("Calling Ajax");
            ajax: {
                url: "/ajaxes/getselect2data/Campaigns",
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

//</script>
<?php $this->Html->scriptEnd(); ?>


<?php echo $this->element('Select2Ajaxelement', array('token' => '88975478KLU96C32', 'name'=>"test_name", 'id'=>"test_id")); ?>


