function deleteCustomer(url,customerid){
    if(confirm("Are you sure you want to delete this customer?")){
        ajaxUpdate(url,{"customerid":customerid},deleteCustomerResponse,'delete');
    }
}
function deleteCustomerResponse(responseText, statusText){
    hideLoader();
    if(statusText == "success") {
        if(responseText.type=="success"){
            $("#notify").notification({caption: responseText.message, type:responseText.type, sticky:false, onhide:function(){
                RefreshLocation();
            }});
        }
        else{
            $("#notify").notification({caption: responseText.message, type:responseText.type, sticky:true});
        }
    }
    else {
        $("#notify").notification({caption: 'Sorry, Unable to communicate with server. Please try again later.', type:'error', sticky:true});
    }
}
$(function() {
    $('.datepicker').each(function(){
        bindDatePicker($(this));
    });
});
function SubmitFunction(id,this_obj){
    $("#submit_form_"+id).ajaxSubmit({
        beforeSubmit:function(){
            $(".error").hide();
            $(this_obj).btnSpinner();
        },
        error:function(requestObject, error, errorThrown){
            console.log(requestObject);
            $(this_obj).btnSpinner({disabled:false});
            if(requestObject.status==422){
                setFieldErrors(requestObject.responseJSON.errors,id);    
            }
            else{
                notyf.error("Sorry, the system encountered an error. Please try again later.");
            }
            
        },
        success:function(response, statusText){
            $(this_obj).btnSpinner({disabled:false});
            if(response.type == "success"){
                notyf.success(response.message);
            }
            else{
                notyf.error(response.message);
            }
        },
        dataType: 'json'
    });
}
function setFieldErrors(errors,form_id){
    $(".error").hide();
    $.each(errors, function(key, value) {
        var splits = key.split(".");
        var key_name = "";
        $.each(splits, function(key, value) {
            if(key == 0){
                key_name += value;
            }
            else{
                key_name += '['+value+']';
            }
        });
        var msg = "";

        $.each( value, function(k,val) {
            msg += '<small class="error" data-fv-for="'+key_name+'" data-error-for="'+key_name+'">'+val+'</small>';
        });
        $('#submit_form_'+form_id).find("input[name='"+key_name+"'],textarea[name='"+key_name+"'],select[name='"+key_name+"']").closest('.input-group').after(msg);
    });
}