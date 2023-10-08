<!--     $('#addnewform').formValidation({
            framework: 'bootstrap',
            excluded: ':disabled',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                datafield: {
                    validators: {
                        notEmpty: {
                            message: 'Data must be needed.'
                        },
                         remote: {
                            enabled: true,
                            url: '/settings/validateform/groups',
                            type: 'POST',
                            delay: 300     // Send Ajax request every 2 seconds
                        },
                        stringLength: {
                            max: 15,
                            message: 'Hostname lenght exeeded.'
                        },

                    }
                }
                
            } //end of fields
    })
    .on('success.form.fv', function(e) {
           e.preventDefault();
            submitform();
        });
    
    
    
  });-->