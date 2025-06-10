function deleteTrashOrder(url,customer_order_id){
    if(confirm("Are you sure you wish to permanently delete this order record?")){
        ajaxUpdate(url,{"id":customer_order_id},deleteTrashOrderResponse,'delete');
    }
}
function deleteTrashOrderResponse(responseText, statusText){
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
function restoreTrashOrder(url,customer_order_id){
    if(confirm("Are you sure you want to restore this order record?")){
        ajaxUpdate(url,{"id":customer_order_id},restoreTrashOrderResponse);
    }
}
function restoreTrashOrderResponse(responseText, statusText){
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

function formsubmit() {
    $('#form-filter').submit();
}
$(function() {
    $('.datepicker').each(function(){
        bindDatePicker($(this));
    });
});
function printList(url) {
    window.open(url, '_blank', 'width=910,height=755')
}