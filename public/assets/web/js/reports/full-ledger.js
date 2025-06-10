$(function() {
    $('.datepicker').each(function(){
        bindDatePicker($(this));
    });

    $('.js-example-select-multiple').select2({
        placeholder: 'Select',
        allowClear: true,
        width: '100%'
    });    
});

function fetchMarka(thisobj) {
    var item_id = thisobj.value;
    var customer_id = $('input#customer_id').val();
    hideLoader();
    if(item_id.length>0){
        ajaxUpdate('/api/item-marka/fetchCustomerMarka',{'id':item_id,'customer_id':customer_id},function(data,textStatus){
            if(textStatus=="success"){
                html = "<option value=''>Select</option>";
                for(var key in data) {
                    html += "<option value=" + data[key].marka_id + ">" + data[key].name + "</option>"
                }
                $(thisobj).parent().siblings('div').find("select[id='marka_id']").each(function() {
                    $(this).html(html);                                     
                });                             
                hideLoader();
            }
        },"GET");
    }
}

function formsubmit() {
    $('#form-filter').submit();
}

function printReport() {
    window.print();
}

function exportReport(url) {    
    hideLoader();
    ajaxUpdate(url,{'data':''},function(data,textStatus){        
        if(textStatus=="success"){                                    
            location.href = data.url;                        
            //console.log(data);                        
        }    
        else {
            $("#notify").notification({caption: data.message, type:data.error, sticky:true});           
        }
        hideLoader();                
    },"POST");    
}