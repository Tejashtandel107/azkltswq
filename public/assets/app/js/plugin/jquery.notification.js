(function($) {
    $.fn.notification = function(options) {
        var opts = $.extend({}, $.fn.notification.defaults, options);
        return this.each(function() {
            $this = $(this);
            var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
            $this.hide();
            $($this).removeAttr("class");
            $this.html('');
            if (o.closebutton) {
                $this.append('<button class="close" data-dismiss="alert" aria-label="Close"></button>');
            }
            if (o.type == "warning") {
                $this.addClass("alert alert-warning alert-dismissible has-icon");
                $this.append('<i class="la la-warning alert-icon" aria-hidden="true"></i>');
            } else if (o.type == "information") {
                $this.addClass("alert alert-info alert-dismissible has-icon");
                $this.append('<i class="la la-info alert-icon"></i>');
            } else if (o.type == "error") {
                $this.addClass("alert alert-danger alert-dismissible has-icon");
                $this.append('<i class="fa fa-exclamation-circle alert-icon"></i>');
            } else if (o.type == "success") {
                $this.addClass("alert alert-success alert-dismissible has-icon");
                $this.append('<i class="la la-check alert-icon"></i>');
            } else {
                $this.addClass("alert alert-primary alert-dismissible has-icon");
                $this.append('<i class="la la-bell alert-icon"></i>');
            }
            $this.append("<strong>" + o.caption + "</strong>");
            if (o.messages.length > 0) {
                $this.append("<ul style='list-style-position:inside;padding:0px;'></ul>");
                for (var msg = 0; msg < o.messages.length; msg++) {
                    $this.find("ul").append("<li id='" + o.messages[msg].id + "'>" + o.messages[msg].message + "</li>");
                }
            }
            if (o.sticky) {
                $this.fadeIn(1000, o.onshow);
            } else {
                $this.fadeIn(1000, function() {
                    var $obj = $(this);
                    o.onshow();
                    setTimeout(function() {
                        $obj.fadeOut(500, o.onhide);
                    }, o.hidedelay);
                });
            }
        });
    };

    function debug($obj) {
        if (window.console && window.console.log)
            window.console.log('hilight selection count: ' + $obj.size());
    };

    function onnotify_show() {};

    function onnotify_hide() {};
    $.fn.notification.defaults = {
        caption: "Warning!",
        sticky: false,
        hidedelay: 3000,
        type: "error",
        messages: [],
        helpmessage: "Do not show this help again.",
        closebutton: true,
        onshow: onnotify_show,
        onhide: onnotify_hide,
        showhelp: function() {}
    };
})(jQuery);
