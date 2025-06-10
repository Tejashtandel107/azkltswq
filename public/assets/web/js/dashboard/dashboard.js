$(function() {
    binddatepicker();
});

function binddatepicker() {
    $('.datepicker').each(function(){
        bindDatePicker($(this));
    });
}

function onChangeYear() {
    var from_date = $('#from_date').val();;
    var to_date = $('#to_date').val();;

    hideLoader();        
    ajaxFetch('/admin/showstatistics',{'from':from_date,'to':to_date},function(data,textStatus) {
        hideLoader();
        if(textStatus=="success"){
            $('#statistics-div').html(data);
            binddatepicker();
        }            
    }, function(XMLHttpRequest, textStatus, errorThrown) {
        hideLoader();
        console.log("ERROR: " + XMLHttpRequest.statusText + " " + "/admin/showstatistics");
        console.log("ERROR: " + XMLHttpRequest.errorThrown + " " + "/admin/showstatistics");
    },"POST");
    
}