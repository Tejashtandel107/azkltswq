$(function() {
    $('.datepicker').each(function(){
        bindDatePicker($(this));
    });
});

function formsubmit() {
    $('#form-filter').submit();
}

function printReport() {
    window.print();
}