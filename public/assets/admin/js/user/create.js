var form_details = profile_form = reset_pass_form =company_form_submit ="";
$(function() {
    $('#user-form').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#submitbtn',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
            firstname: {
                validators: {
                    notEmpty: {
                        message: 'Please enter First Name'
                    }
                }
            },
            lastname: {
                validators: {
                    notEmpty: {
                        message: 'Please enter Last Name'
                    }
                }
            },
            username: {
                validators: {
                    notEmpty: {
                        message: 'Please enter User Name'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_.]+$/,
                        message: 'The username must not have blank space and special characters like ! " # $ % @ & ’ ( ) *'
                    }
                }
            },
             email: {
                validators: {
                    notEmpty: {
                        message: 'Please enter Email'
                    },
                    emailAddress: {
                        message: 'Please enter valid Email Address'
                    }
                }
            },
            photo: {
                 validators: {
                     file: {
                        extension: 'jpeg,jpg,png,bmp,gif,svg',
                         type: 'image/jpeg,image/png,image/bmp,image/gif,image/svg',
                        message: 'The selected file is not valid please select image file only'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'Please enter new password'
                    },
                    stringLength: {
                        min: 6,
                    }
                }
            },
            password_confirmation: {
                validators: {
                    notEmpty: {
                        message: 'Please enter new password'
                    },
                    identical: {
                        field: 'password',
                        message: 'New Password and Confirm Password must be same'
                    }
                }
            },
            // photo: {
            //     validators: {
            //         file: {
            //             extension: 'jpeg,jpg,png,bmp,gif,svg',
            //             type: 'image/jpeg,image/png,image/bmp,image/gif,image/svg',
            //             message: 'The selected file is not valid please select image file only'
            //         }
            //     }
            // },
        },
        err: {
            clazz: 'error'
        },
        row: {
            invalid: '',
            valid:''
        },
        onSuccess: function(e) {
            e.preventDefault();
            form_details = e.target;
            $(e.target).ajaxSubmit({
                beforeSubmit:beforeformRequest,
                error:onAjaxCallError,
                success:formResponse,
                dataType: 'json'
            });
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
    $('#reset-password-form').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#resetsubmit',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
            oldpassword: {
                validators: {
                    notEmpty: {
                        message: 'Please enter current password'
                    },
                    stringLength: {
                        min: 6,
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'Please enter new password'
                    },
                    stringLength: {
                        min: 6,
                    }
                }
            },
            password_confirmation: {
                validators: {
                    notEmpty: {
                        message: 'Please enter new password'
                    },
                    identical: {
                        field: 'password',
                        message: 'New Password and Confirm Password must be same'
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
        },
        onSuccess: function(e) {
            e.preventDefault();
            form_details = e.target;
            $(e.target).ajaxSubmit({
                beforeSubmit:beforeformRequest,
                error:onAjaxCallError,
                success:formResponse,
                dataType: 'json'
            });
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

function beforeformRequest(formData, jqForm, options) {
    showLoader();
}

function onAjaxCallError(requestObject, error, errorThrown){
    hideLoader();
    var current_form_id = $(form_details).parent().parent().attr('id');

    if(requestObject.status == 422 || errorThrown=='Unprocessable Entity'){
        var msgs=[];
        var response = $.parseJSON(requestObject.responseText);
        $.each(response.errors, function (key, val) {
            msgs.push({ message: val });
        });
        /*jQuery.each(requestObject.responseJSON, function(i, val) {
            msgs.push({ message: val });
            console.log(i);
        });*/

        $("#"+current_form_id+" #notify").notification({caption: "One or more invalid input found.", messages: msgs, sticky:true});
    }
}

function formResponse(responseText, statusText) {
    hideLoader();
    var current_form_id = $(form_details).parent().parent().attr('id');
    $(form_details).data('formValidation').disableSubmitButtons(false);

    if(statusText == "success") {
        if(responseText.type=="success"){
            if(responseText.imageLocation!=undefined){
                $("#"+current_form_id+" .profile-img").attr('src',responseText.imageLocation);
                //$("#"+current_form_id+" .profile-img").replaceWith("<img id='company-logo' width='90' height='90' class='img-circle profile-img' src='" + responseText.imageLocation + "' />");
            }
            if(responseText.redirectUrl){
                $("#"+current_form_id+" #notify").notification({caption: responseText.message, type:responseText.type, sticky:false,closebutton:false,onhide:function(){
                    window.location.href=responseText.redirectUrl;
                }});
            }
            $("#"+current_form_id+" #notify").notification({caption: responseText.message, type:responseText.type, sticky:false,closebutton:false});

        }
        else{
            $("#"+current_form_id+" #notify").notification({caption: responseText.message, type:responseText.type, sticky:true,closebutton:false});
        }
    }
}