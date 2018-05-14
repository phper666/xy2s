;(function () {
    /**
     * 页面跳转
     * @param url
     */
    function redirect(url) {
        location.href = url;
    }

    /**
     * 读取cookie
     * @param name
     * @returns
     */
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) == 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }


        return null;
    }

// 设置cookie
    function setCookie(name, value, days) {
        var argc = setCookie.arguments.length;
        var argv = setCookie.arguments;
        var secure = (argc > 5) ? argv[5] : false;
        var expire = new Date();
        if (days == null || days == 0) days = 1;
        expire.setTime(expire.getTime() + 3600000 * 24 * days);
        document.cookie = name + "=" + escape(value) + ("; path=/") + ((secure == true) ? "; secure" : "") + ";expires=" + expire.toGMTString();
    }

    /**
     * 打开iframe式的窗口对话框
     * @param url
     * @param title
     * @param options
     */
    function open_iframe_dialog(url, title, options) {
        var params = {
            title: title,
            lock: true,
            opacity: 0,
            width: "95%",
            height: '90%'
        };
        params = options ? $.extend(params, options) : params;
        Wind.use('artDialog', 'iframeTools', function () {
            art.dialog.open(url, params);
        });
    }

});

