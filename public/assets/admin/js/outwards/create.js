var json = '';
$(function() {
    //Bind datepicker
    $('.datepicker').each(function(){
        bindDatePicker($(this));        
    });    

    /** 
     * Bind Type Ahead Plugin
     * bindTypeAheadPlugin($("#livesearchvakkalnumber"));
    */
});

function OnFormSubmit(thisobj,printconfirm) {
    if(printconfirm){
        if (!confirm('Do you want to take print?\nClick Yes/Ok for Print and Cancel for No' )) {
            document.getElementById('printoutward').value = 0;
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

//remove template
function removeItem(thisobj) {
    var $row = $(thisobj).closest('.display-group');
    $row.remove();
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
                $(this).html(html);                                     
            });                             
            hideLoader();
        }
    },"GET");    
}


//on select customer, populate address field 
function ChangeAddress(thisobj) {    
    var customer = thisobj.value;
    var $address = $('.address'); 
    $address.val('');
    hideLoader();
    if(customer>0){
        ajaxUpdate('/api/customers/getCustomer',{'cid':customer},function(data,textStatus){
            if(textStatus=="success"){
                $address.val(data.address);
                hideLoader();
            }
        },"GET");
    }
}

//On click "Add" button, get inward entry by order_item_id and append the html response.
function getInwardData(order_item_id) {
    if(order_item_id>0){
        ajaxFetch('/api/inwards/getInward',{'order_item_id':order_item_id},function(data,textStatus){
            if(textStatus=="success"){          
                $('#adddivision').append(data);
                hideLoader();
            }
        },'',"GET",true);
    }
}


// Ajax search inwards, by filters.
function ajaxsearchfilter() {
    var $search = $('.search').val();    
    var $from = $('.from').val();    
    var $to = $('.to').val();    
    var $item = $('.item').val();    
    var $marka = $('.marka').val();
    var $customer_id = $('.customer_id').val();    
    var $dataObj = {"search":$search, "from": $from, "to":$to, "item":$item, "marka":$marka, "customer_id":$customer_id };

    hideLoader();    
    ajaxFetch('/api/inwards/search',{ data:$dataObj },function(data,textStatus){
        if(textStatus=="success"){
            $('.addlist').html(data);               
            hideLoader();
        }
    },'',"POST",true);    
}


/* Auto Suggest Search by Vakkal Number.
function bindTypeAheadPlugin(selector) {
    var suggestionEngine = new Bloodhound({
        datumTokenizer: function (d) {return Bloodhound.tokenizers.whitespace(d.value);},
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '/api/inwards/search',
            replace: function (url, uriEncodedQuery) {
                return url + "?search=" + uriEncodedQuery;
            },
            transform: function (response) {
                return response;
            }
        }
    });
    var suggestionTemplate = function (data) {
        return '<div class="item"><div class="name"><div><b>Vakkal: &nbsp;</b>' + data.vakkal_number + 
        '</div><div><b>Item: &nbsp;</b>' + data.item_name + '</div><div><b>Marka: &nbsp;</b>' + data.marka_name + 
        '</div><div><b>Customer: &nbsp;</b>' + data.companyname + '</div></div></div>';
    };
    $(selector).typeahead({
            highlight: true,
            minLength: 1
        },
        {
        name: 'page',
        display: 'title',
        source: suggestionEngine,
        templates: {
            notFound: '<p align="center">Sorry, not found </p>',
            suggestion: suggestionTemplate,
            pending: '<p align="center">Loading...</p>'
        }
    }).bind('typeahead:select', function (ev, suggestion) {
        getInwardData(suggestion.vakkal_number);
        $(this).typeahead('val', suggestion.vakkal_number);
        //FilterSubmit();
    });
}
*/
