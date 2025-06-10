var form_details = profile_form = reset_pass_form =company_form_submit ="";
$(function() {
    $('#customer-form').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#submitbtn',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
            companyname: {
                validators: {
                    notEmpty: {
                        message: 'Please enter Company Name'
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
});

function openModalPopUp(url) {
    ajaxPopup(url, function(){configureModal()});
}
function deleteUser(url,user_id){
    if(confirm("Are you sure you want to delete this user?")){
        ajaxUpdate(url,{"user_id":user_id},deleteUserResponse,'delete');
    }
}
function deleteUserResponse(responseText, statusText){
    hideLoader();
    if(statusText == "success") {
        if(responseText.type=="success"){
            $("#deletenotify").notification({caption: responseText.message, type:responseText.type, sticky:false, onhide:function(){
                RefreshLocation();
            }});
        }
        else{
            $("#deletenotify").notification({caption: responseText.message, type:responseText.type, sticky:true});
        }
    }
    else {
        $("#deletenotify").notification({caption: 'Sorry, Unable to communicate with server. Please try again later.', type:'error', sticky:true});
    }
}
function beforeformRequest(formData, jqForm, options) {
    showLoader();
}

function onAjaxCallError(requestObject, error, errorThrown){
    var current_form_id = $(form_details).parent().parent().attr('id');
    if(requestObject.status == 422 || errorThrown=='Unprocessable Entity'){
        var msgs=[];
        jQuery.each(requestObject.responseJSON, function(i, val) {
            msgs.push({ message: val });
        });
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