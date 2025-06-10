var json = '';
$(function() {
    //Bind datepicker
    $('.datepicker').each(function(){
        bindDatePicker($(this));        
    });    
});

function OnFormSubmit(thisobj,printconfirm) {    
    if(printconfirm){
        if (!confirm('Do you want to take print?\nClick Yes/Ok for Print and Cancel for No' )) {
            document.getElementById('printinward').value = 0;
        }
    }
    $(thisobj).ajaxSubmit({
        beforeSubmit:beforeformRequest,
        error:onAjaxCallError,
        success:formResponse,
        dataType: 'json'
    });
    return false;
}
function beforeformRequest(formData, jqForm, options) {    
    $('button[id=submitbtn]').prop('disabled', true);
    $('button[id=submitbtn]').html('SAVING...');
    showLoader();
}

function onAjaxCallError(requestObject, error, errorThrown){
    $('button[id=submitbtn]').prop('disabled', false);
    $('button[id=submitbtn]').html('SAVE');
    hideLoader();
    if(errorThrown=='Unprocessable Entity'){
        var msgs=[];
		jQuery.each(requestObject.responseJSON.errors, function(i, val) {
			msgs.push({ message: val });
		});
        $("#notify").notification({caption: "One or more invalid input found.", messages: msgs, sticky:true});        
    }
}

function formResponse(response, statusText) {
    $('button[id=submitbtn]').prop('disabled', false);
    $('button[id=submitbtn]').html('SAVE');
    hideLoader();
    $('.to-top').click();
    if(statusText == "success") {
        if(response.type=="success"){
            $("#notify").notification({caption: response.message, type:response.type, sticky:false, onhide:function(){
                location.href=response.redirect;                        
            }});
        }
        else{
            $("#notify").notification({caption: response.message, type:response.type, sticky:true});
        }
    }
    else {
        $("#notify").notification({caption: 'Sorry, Unable to communicate with server. Please try again later.', type:'error', sticky:true});
    }
}


//Add template
function addItem(thisobj) {
    var $template = $('#ItemTemplate'),
        $clone    = $template.clone().removeClass('hide').removeAttr('id').insertBefore($template);
        $clone.find('.form-control').removeAttr('disabled');    
}

$("#wishlist_model_form").ajaxForm({
    beforeSubmit:beforeformRequest,
    error:onAjaxCallError,
    success:formResponse
});

//remove template
function removeItem(thisobj) {
    var $row = $(thisobj).closest('.display-group');
    $row.remove();
}

//open marka create modal
function openMarkaModalPopUp(url) {
    ajaxPopup(url, function(){configureModal()});
}

//open item create modal
function openItemModalPopUp(url) {
    ajaxPopup(url, function(){configureModal()});
}

//open customer create modal
function openCustomerModalPopUp(url) {
    ajaxPopup(url, function(){configureModal()});
}

//on Item select fetch Marka
function fetchMarka(thisobj) {
    var item_id = thisobj.value;
    hideLoader();
    ajaxUpdate('/api/item-marka/fetchMarka',{'id':item_id},function(data,textStatus){
        if(textStatus=="success"){
            html = "<option value='' selected>Select</option>";
            for(var key in data) {
                html += "<option value=" + data[key].marka_id + ">" + data[key].name + "</option>"
            }
            $(thisobj).parent().siblings('div').find("select[id='marka_id']").each(function() {
                //var selected_val = $(this).find('option:selected').val();
                $(this).html(html);                                     
                
                // if(selected_val>0) {
                //     $(this).val(selected_val);                        
                // }else {
                //     $(this).val("");                        
                // }                
            });                             
            hideLoader();
        }
    },"GET");    
}