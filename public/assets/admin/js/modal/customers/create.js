function configureModal(){
    $('#add_customer_form').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#submitbtn'
        },
        icon: null,
        fields: {
            companyname: {
                validators: {
                    notEmpty: {
                        message: 'Please enter Party Name'
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
            
            // Append newly created item to the list
            obj = responseText.customers;                    
            $("select[id='customer_id']").append($("<option></option>").attr("value",obj.customer_id).text(obj.companyname)); 
            
            // Fetch the list, sort and append
            $("select[id='customer_id']").each(function() {               
                var selector = $(this);
                var selected = selector.find('option:selected').val();                
                var opts_list = selector.find("option[value!='']");            
                opts_list.sort(function(a, b) { return $(a).text().toLowerCase() > $(b).text().toLowerCase() ? 1 : -1; });                                
                selector.html("<option value=''>Select</option>").append(opts_list);                
                if(selected>0) {
                    selector.val(selected);
                }else {
                    selector.val(obj.customer_id); 
                }                
            });                  
        }});
    }
    else{
        $("#modalnotify").notification({caption: responseText.message, type:responseText.type, sticky:true});

    }
}

