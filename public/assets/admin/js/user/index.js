function deleteUser(url,userid){
    if(confirm("Are you sure you want to delete this Admin?")){
        ajaxUpdate(url,{"user_id":userid},deleteUserResponse,'delete');
    }
}
function deleteUserResponse(responseText, statusText){
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
