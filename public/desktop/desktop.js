/**
 * 模板填充工具
 */
var substitute = function(str, obj) {
    return str.replace(/\\?\{([^{}]+)\}/g, function(match, name) {
        if (match.charAt(0) == '\\') return match.slice(1);
        return (obj[name] != undefined) ? obj[name] : '';
    });
};

! function() {
    /**
     * Jquery 个性化
     */
    var r20 = /%20/g,
        rbracket = /\[\]$/,
        rCRLF = /\r?\n/g,
        rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i,
        rsubmittable = /^(?:input|select|textarea|keygen)/i;

    function buildParams(prefix, obj, traditional, add) {
        var name;

        if (jQuery.isArray(obj)) {
            // Serialize array item.
            jQuery.each(obj, function(i, v) {
                if (traditional || rbracket.test(prefix)) {
                    // Treat each array item as a scalar.
                    add(prefix, v);

                } else {
                    // Item is non-scalar (array or object), encode its numeric index.
                    buildParams(prefix + "[" + (typeof v === "object" ? i : "") + "]", v, traditional, add);
                }
            });

        } else if (!traditional && jQuery.type(obj) === "object") {
            // Serialize object item.
            for (name in obj) {
                buildParams(prefix + "[" + name + "]", obj[name], traditional, add);
            }

        } else {
            // Serialize scalar item.
            add(prefix, obj);
        }
    }

    // Serialize an array of form elements or a set of
    // key/values into a query string
    jQuery.param = function(a, traditional) {
        var prefix,
            s = [],
            add = function(key, value) {
                // If value is a function, invoke it and return its value
                value = jQuery.isFunction(value) ? value() : (value == null ? "" : value);
                s[ s.length ] = key + "=" + encodeURIComponent( value );
                //s[s.length] = (key) + "=" + (value);
            };

        // Set traditional to true for jQuery <= 1.3.2 behavior.
        if (traditional === undefined) {
            traditional = jQuery.ajaxSettings && jQuery.ajaxSettings.traditional;
        }

        // If an array was passed in, assume that it is an array of form elements.
        if (jQuery.isArray(a) || (a.jquery && !jQuery.isPlainObject(a))) {
            // Serialize the form elements
            jQuery.each(a, function() {
                add(this.name, this.value);
            });

        } else {
            // If traditional, encode the "old" way (the way 1.3.2 or older
            // did it), otherwise encode params recursively.
            for (prefix in a) {
                buildParams(prefix, a[prefix], traditional, add);
            }
        }

        // Return the resulting serialization
        return s.join("&").replace(r20, "+");
    };
}();

/**
 * 高级模板填充工具
 */
var Tool = {
    templateEngine: function(html, options) {
        var re = /{([^}]+)?}/g,
            reExp = /(^( )?(if|for|else|switch|case|break|{|}))(.*)?/g,
            code = 'var r=[];\n',
            cursor = 0;
        var add = function(line, js) {
            js ? (code += line.match(reExp) ? line + '\n' : 'r.push(this.' + line + ');\n') :
                (code += line != '' ? 'r.push("' + line.replace(/"/g, '\\"') + '");\n' : '');
            return add;
        }
        while (match = re.exec(html)) {
            add(html.slice(cursor, match.index))(match[1], true);
            cursor = match.index + match[0].length;
        }
        add(html.substr(cursor, html.length - cursor));
        code += 'return r.join("");';
        return new Function(code.replace(/[\r\t\n]/g, '')).apply(options);
    }
};
/**
 * 进入后台时加载
 */
jQuery(document).ready(function() {
    Metronic.init();
    Layout.init();
    window.load_page = function(url, container, params, callback) {

        container = container || $('[data-updatecontainer="default"]');
        var data = false;
        if (params && typeof params === 'string') {
            $.each(params.split('&'), function(i, kv) {
                kv = kv.split('=');
                if (kv[1]) {
                    if (typeof data !== 'object') data = {};
                    data[kv[0]] = kv[1];
                }
            });
        }
        BlockLoading({
            target: container,
            animate: true
        });

        container.load(url, data, function(re, f, obj) {
            if (container.is('div[data-updatecontainer="default"]')) pushState(url);
            if (callback && typeof callback === 'function') {
                callback();
            }

        });

    };

    $(document.body).on('click', 'a:not(a[target="_blank"],a[href^="javascript:"],a[data-toggle],a[data-target],a[data-event])', function(e) {
        e.stopPropagation();
        if ($(this).attr('data-confirm') && !confirm($(this).attr('data-confirm'))) {
            return false;
        }
        var _this = $(this);
        var url = _this.prop('href');
        var target = _this.prop('target');
        var c = _this.closest('[data-updatecontainer]');
        if (target == '_command') {
            $.ajax({
                url: url,
                success: function(responseText) {
                    jsonCommond(responseText);
                },
                error: function(xhr) {
                    Messagebox.error('STATUS:' + xhr.status, '请求异常');
                }
            });
            return false;
        }
        if (c.length)
            load_page(url, c);
        else
            load_page(url);
        return false;
    });


    var lastState = '';

    var pushState = function(url) {
        var _v = url.match('index\\.php\\?(.*)');
        _v = _v[1];

        if (typeof history.pushState != 'undefined' && _v) {
            history[lastState == url ? 'replaceState' : 'pushState']({
                    go: _v
                },
                '', '#' + encodeParams(_v));
            lastState = url;
        }
    }
    var decodeParams = function(str) {
        var _return = '';
        $.each(str.split('-'), function(i, n) {
            var kv = n.split(':');
            _return += kv[0] + '=' + kv[1] + '&';

            if (kv[0] == 'close_sidebar' && kv[1] == '1') {
                $('body').addClass('page-sidebar-closed');
                $('.page-sidebar-menu').addClass('page-sidebar-menu-closed');
            }
        });
        return _return.slice(0, -1);
    };
    var encodeParams = function(str) {

        var _return = '';
        $.each(str.split('&'), function(i, n) {
            var kv = n.split('=');
            _return += kv[0] + ':' + kv[1] + '-';
        });
        return _return.slice(0, -1);
    }
    window.onpopstate = function(event) {
        if (!event.state) return;
        var params = event.state.go;
        if (!params || params == '') {
            params = 'app=desktop&ctl=dashboard'
        }
        $('[data-updatecontainer="default"]').load('index.php?' + params);
    };

    load_page(location.hash.slice(1) ? 'index.php?' + decodeParams(location.hash.slice(1)) : 'index.php?app=desktop&ctl=dashboard')

});
/**
 * 处理STR 2 JSON
 */
jsonDecode = function(re) {
    if (typeof(re) == 'object') {
        return re;
    }
    try {
        re = JSON.parse(re);
        if (!re || typeof(re) != 'object')
            re = $.parseJSON(re);
        if (!re || typeof(re) != 'object')
            return false;
    } catch (e) {
        return false;
    }
}
/**
 * 左侧菜单高亮
 */
var activeMenu = function(path) {
    path = path.split(':');
    $('.page-sidebar-menu li.active,.page-sidebar-menu li.open').removeClass('active').removeClass('open');
    $('.page-sidebar-menu li[data-wmenu="' + path[0] + '"]').addClass('active open');
    $('.page-sidebar-menu li[data-menu="' + path[1] + '"]').addClass('active');
    if (path[0] <= 0) {
        $('.page-sidebar-menu .sub-menu').css('display', 'none');
    }
};

var BlockLoading = Metronic.blockUI,
    UnblockLoading = Metronic.unblockUI,
    Messagebox = toastr;

/**
 * 处理ajax 消息
 */
var jsonCommond = function(response) {

    re = jsonDecode(response);
    if (!re) {
        return Messagebox.error('操作失败!' + response, '异常');
    }
    if (re.success) {
        Messagebox.success(re.success, '成功');
        if (re.redirect) {
            load_page(re.redirect);
        }
        return;
    }
    if (re.error) {
        Messagebox.error(re.error, '错误');
    }
};
/**
 * 拦截页面表单提交
 */
$(document.body).on('submit', 'form:not(form[target])', function(e) {
    e.stopPropagation();
    var form = $(this);
    BlockLoading({
        target: form,
        animate: true
    });
    if (form.attr('enctype') == 'multipart/form-data') {
        //文件提交
        var iframe_uid = 'iframe_' + new Date().getTime();

        var iframe = $('<iframe name="' + iframe_uid + '" src="about:blank" style="display:none;"></iframe>');
        iframe.appendTo($(document.body));
        form.attr('target', iframe_uid);
        form.submit();
        iframe.on('load', function() {
            UnblockLoading(form);
            form.removeProperty('target');
            setTimeout(function() {
                iframe.remove()
            }, 2000);
        });
        return false;
    }
    $.ajax({
        url: form.prop('action'),
        type: form.prop('method') ? form.prop('method') : 'post',
        data: form.serialize(),
        beforeSend: function(xhr) {
            if (form.data('ajax:beforeSend') && typeof(form.data('ajax:beforeSend')) == 'function') {
                form.data('ajax:beforeSend')(xhr);
            }
        },
        success: function(responseText) {
            UnblockLoading(form);
            jsonCommond(responseText);
            if (form.data('ajax:success') && typeof(form.data('ajax:success')) == 'function') {
                form.data('ajax:success')(responseText);
            }
        },
        complete: function(xhr) {
            UnblockLoading(form);
            if (form.data('ajax:complete') && typeof(form.data('ajax:complete')) == 'function') {
                form.data('ajax:complete')(xhr);
            }
        },
        error: function(xhr) {
            Messagebox.warning(xhr, '异常');
            if (form.data('ajax:error') && typeof(form.data('ajax:error')) == 'function') {
                form.data('ajax:error')(xhr);
            }
        }
    });

    return false;

});
//bootbox language defaults
bootbox.setDefaults({
    locale: 'zh_CN'
});

/**
 * 处理回车提交
 */
$(document).on('keydown',function(e){
    if(e.keyCode == 13 && $(e.target).is('input') && e.target.form){
        try{
            if($(e.target).data('events')['keydown']){
                return true;
            }
        }catch(e){

        }finally{

        }

        e.stopPropagation();
        return false;
    }
});

/**
 * 新增粘贴事件
 *
 *    $("input[type='text']").on("postpaste", function() {
 *        // code...
 *    }).pasteEvents();
 *
 *
 */
 $.fn.pasteEvents = function( delay ) {
    if (delay == undefined) delay = 20;
    return $(this).each(function() {
        var $el = $(this);
        $el.on("paste", function() {
            $el.trigger("prepaste");
            setTimeout(function() { $el.trigger("postpaste"); }, delay);
        });
    });
};


/**
 * 全屏事件
 */
$(function(){
    var _fullscreen = function(){
        var flag = false;
        $.each(['requestFullscreen','mozRequestFullScreen','webkitRequestFullScreen','msRequestFullscreen'],function(i,f){
            if(!flag && (f in document.documentElement)){
                document.documentElement[f]();
                flag = true;
            }
        });
        return flag;
    };
    $('.full-screen-handle').on('click',function(e){
        e.stopPropagation();
        _fullscreen();
    });

});
