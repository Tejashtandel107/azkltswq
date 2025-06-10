function showToastMessage(toast, settings) {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-full-width",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "0",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    var caption = "";
    if (toast.caption != "" && toast.caption != 'undefined') {
        var caption = toast.caption;
    }
    var message = "";
    if (toast.message != "" && toast.message != 'undefined') {
        var message = toast.message;
    }

    if (typeof settings != "undefined" && settings != null){
        if (typeof settings.timeOut != "undefined" || settings.timeOut != null){
            toastr.options.progressBar = true;
        }
    }

    toastr.options = $.extend({}, toastr.options, settings);
    return toastr[toast.type](message, caption)
}
function clearToast(toast){
    if (typeof toast != "undefined" && toast != null){
        toastr.clear(toast);
    }
    toastr.clear();
}
function showModal() {
    var $modal = $('#modalwrp #modalmain').modal();
    $modal.modal('show');
}
function hideModal() {
    $('#modalmain').modal('hide');
}
function showLoader() {
    $(".loader").show();
}
function hideLoader() {
    $(".loader").hide();
}
function RefreshLocation() {
    location.reload();
}
function str_replace(search, replace, subject, count) {
    var i = 0,
        j = 0,
        temp = '',
        repl = '',
        sl = 0,
        fl = 0,
        f = [].concat(search),
        r = [].concat(replace),
        s = subject,
        ra = Object.prototype.toString.call(r) === '[object Array]',
        sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);
    if (count) {
        this.window[count] = 0;
    }
    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue;
        }
        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if (count && s[i] !== temp) {
                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }
    return sa ? s : s[0];
}

function redirectAfter(url, miliseconds) {
    setTimeout(function() {
        redirectToUrl(url);
    }, miliseconds);
}
function redirectToUrl(url){
    window.location.href = url;
}
function openPopCenter(url) {
    var leftPosition, topPosition;
    var screenwidth = window.screen.width;
    var screenheight = window.screen.height;
    var width = (screenwidth / 2);
    var height = (screenheight / 2);
    leftPosition = (screenwidth / 2) - ((width / 2) + 10);
    topPosition = (screenheight / 2) - ((height / 2) + 50);
    window.open(url, "Window2", "height=" + height + ",width=" + width + ",resizable=yes,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition);
}
/* Ajax Functions Starts */
function ajaxCall(interfaceUrl, interfaceID, loaderID, callBack) {
    showLoader();
    $.ajax({
        type: "GET",
        url: interfaceUrl,
        cache: false,
        dataType: "html",
        success: function(data, textStatus) {
            if (typeof(interfaceID) == 'string') {
                $("#" + interfaceID).hide();
                $("#" + interfaceID).html(data);
                $("#" + interfaceID).fadeIn();
            } else {
                $(interfaceID).html(data);
            }
            hideLoader();
            if (callBack == undefined) {
                ajaxCallback(interfaceID);
            } else {
                callBack(interfaceID);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            hideLoader();
            console.log("ERROR: " + XMLHttpRequest.statusText + " " + Url);
            console.log("ERROR: " + XMLHttpRequest.errorThrown + " " + Url);
        }
    });
}
function ajaxUpdate(Url, data, callBack, method) {
    if (method == undefined || method == '') {
        method = "POST";
    }
    showLoader();
    $.ajax({
        type: method,
        url: Url,
        cache: false,
        data: data,
        dataType: "json",
        success: callBack,
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            hideLoader();
            console.log("ERROR: " + XMLHttpRequest.statusText + " " + Url);
            console.log("ERROR: " + XMLHttpRequest.errorThrown + " " + Url);
        }
    });
}
function ajaxUpdateSimple(Url, data, callBack, method) {
    if (method == undefined || method == '') {
        method = "POST";
    }
    $.ajax({
        type: method,
        url: Url,
        cache: false,
        data: data,
        dataType: "json",
        success: callBack,
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            hideLoader();
            console.log("ERROR: " + XMLHttpRequest.statusText + " " + Url);
            console.log("ERROR: " + XMLHttpRequest.errorThrown + " " + Url);
        }
    });
}
function ajaxPopup(interfaceUrl, callBack, data) {
    showLoader();
    $.ajax({
        type: "GET",
        url: interfaceUrl,
        cache: false,
        data: data,
        dataType: "html",
        success: function(data, textStatus) {
            $("#modalwrp").html(data);
            hideLoader();
            showModal();
            if (callBack) callBack();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            hideLoader();
            console.log("ERROR: " + XMLHttpRequest.statusText + " " + interfaceUrl);
        }
    });
}
function ajaxFetch(interfaceUrl, data, callBack, errorCallback, method, loader) {
    if (loader == undefined || loader == true) {
        showLoader();
    }
    if (method == undefined || method == '') {
        method = "POST";
    }
    $.ajax({
        type: method,
        url: interfaceUrl,
        data: data,
        dataType: "html",
        success: callBack,
        error: errorCallback
    });
}
/* Ajax Functions End */
