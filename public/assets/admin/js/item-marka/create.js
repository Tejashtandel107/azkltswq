$(function() {
    $('.datepicker').each(function(){
        bindDatePicker($(this));
        $(this).on('changeDate', function() {
            $("#marka-form").formValidation('revalidateField', $(this).attr('name'));
        });
    })
    $('#marka-form').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#submitbtn',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
            item_id: {
                validators: {
                    notEmpty: {
                        message: 'Please select Item'
                    }
                },
            },
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please enter Name'
                    }
                }
            },   
        },
        err: {
            clazz: 'error'
        },
        row: {
            invalid: '',
            valid:''
        }
    }).on('err.validator.fv', function(e, data) {
        // $(e.target)    --> The field element
        // data.fv        --> The FormValidation instance
        // data.field     --> The field name
        // data.element   --> The field element
        // data.validator --> The current validator name
    	data.element
        .data('fv.messages')
        // Hide all the messages
        .find('.error[data-fv-for="' + data.field + '"]').hide()
        // Show only message associated with current validator
        .filter('[data-fv-validator="' + data.validator + '"]').show();

    });
});
