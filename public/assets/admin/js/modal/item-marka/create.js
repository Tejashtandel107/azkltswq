function configureModal(){
    $('#add_marka_form').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#submitbtn'
        },
        icon: null,
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please enter Name'
                    }
                }
            },            
            item_id: {
                validators: {
                    notEmpty: {
                        message: 'Please select Item'
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
            // Prevent form submission
            e.preventDefault();

            var $form = $(e.target);

            $($form).ajaxSubmit({
                beforeSubmit:beforeModalformRequest,
                error:onModalAjaxCallError,
                success:ModalformResponse,
                dataType: 'json'});

        }
    });
}
function beforeModalformRequest(formData, jqForm, options) {
    showLoader();
}
function onModalAjaxCallError(requestObject, error, errorThrown){
    hideLoader();
    if(errorThrown=='Unprocessable Entity'){
		var msgs=[];
		jQuery.each(requestObject.responseJSON.errors, function(i, val) {
			msgs.push({ message: val });
		});
		$("#modalnotify").notification({caption: requestObject.responseJSON.message, messages: msgs, sticky:true});
	}
}

function hideModal() {
    $('#modalmain').modal('hide');
}

function ModalformResponse(responseText, statusText) {
    hideLoader();
    if(statusText == "success") {
        $("#modalnotify").notification({caption: responseText.message, type:responseText.type, sticky:false,onhide:function(){
            hideModal();
            // Append the newly created marka at the end of the list.
            obj = responseText.markas;                    
            
            // Fetch the list, sort the list and append.
            $("select[id='marka_id']").each(function() {
                var selector = $(this);  
                var selected = selector.find('option:selected').val();                                        
                var item_value = selector.parent().siblings().children('select[id="item_id"]').val();                
                if(item_value === obj.item_id) {                    
                    selector.append($("<option></option>").attr("value",obj.marka_id).text(obj.name)); 
                }                
                var opts_list = selector.find("option[value!='']");            
                opts_list.sort(function(a, b) { return $(a).text().toLowerCase() > $(b).text().toLowerCase() ? 1 : -1; });                                                
                selector.html("<option value=''>Select</option>").append(opts_list);                                    
                if(selected>0) {
                    selector.val(selected);
                }else {
                    selector.val(obj.marka_id); 
                }
            });                  
        }});
    }
    else{
        $("#modalnotify").notification({caption: responseText.message, type:responseText.type, sticky:true});

    }
}

