// $(function() {
// 	$('#login-form').formValidation({
// 		framework: "bootstrap4",
// 		icon: null,
// 		fields: {
// 			email: {
// 				validators: {
// 					notEmpty: {
// 						message: 'Please enter Username or Email'
// 					}
// 				}
// 			},
// 			password: {
// 				validators: {
// 					notEmpty: {
// 						message: 'Please enter Password'
// 					}
// 				}
// 			}
// 		},
// 		err: {
// 			clazz: 'error'
// 		},
// 		row: {
// 			invalid: '',
// 			valid:''
// 		}
// 	});
// });

$(function() {
    $('#forgot-form1').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '.submitbtn',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
			email: {
				validators: {
					notEmpty: {
						message: 'Please enter Email'
					}
				}
			}
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
            $(e.target).ajaxSubmit({
                beforeSubmit:beforeformRequest,
                error:onAjaxCallError,
                success:formResponse,
                dataType: 'json'
            });
        }
    })
    .on('err.validator.fv', function(e, data) {
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
    $("#notify").notification({caption: "Please wait.",sticky:true});
}

function onAjaxCallError(requestObject, error, errorThrown){
    if(errorThrown=='Unprocessable Entity'){
        var msgs=[];
        jQuery.each(requestObject.responseJSON, function(i, val) {
            msgs.push({ message: val });
        });
        $("#notify").notification({caption: "One or more invalid input found.", messages: msgs, sticky:true});
    }
}

function formResponse(responseText, statusText) {
	console.log("hello");
    if(statusText == "success") {
        if(responseText.type=="success"){
            $("#notify").notification({caption: responseText.message, type:responseText.type, sticky:false,closebutton:false,onhide:function(){
                window.location.href=responseText.redirectUrl;
            }});
        }
        else{
            $("#notify").notification({caption: responseText.message, type:responseText.type, sticky:true,closebutton:false});
        }
    }
}