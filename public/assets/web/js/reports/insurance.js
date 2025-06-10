$(function() {
    // $('.datepicker').each(function(){
    //      format: 'mm-yyyy'
    //     bindDatePicker($(this));
    // });   
    $(".datepicker").datepicker({
        autoclose:true,
        orientation:'left bottom',
        format: "yyyy-mm",
        viewMode: "months", 
        minViewMode: "months",
        endDate: '+0d'
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