var region_sel = {
    addOpt: function(select, data) {
        var fdoc = document.createDocumentFragment();
        fdoc.appendChild($('<option>请选择</option>').attr('value', '_NULL_')[0]);

        data && $.each(data, function(v, k) {
            var attrs = k.split(':');
            fdoc.appendChild($('<option>' + attrs[0] + '</option>').attr({
                value: attrs[1],
                'data-level-index': attrs[2] ? attrs[2] : '_NULL_'
            })[0]);
        });
        (select = $(select)) && select.empty().append(fdoc);
        data && select && select.css('visibility', 'visible');
        return this;
    },
    bindEvent: function() {
        var _this = this,
            sels = this.elem.find('select');
        sels.on('change', function(e) {
            _this.changeResponse(this);
        });
    },
    changeResponse: function(cur_sel, opt) {
        var _this = this,
            sels = this.elem.find('select'),
            level = _this.set(cur_sel, opt),
            elems = $(cur_sel).siblings('select');

        if ($(cur_sel).find('[selected]').attr('data-level-index') == "_NULL_" && _this.callback) {
            _this.callback(sels);
        }
        $.each(elems, function(i, el) {
            el = $(el);
            if (el.attr('data-level-index') <= $(cur_sel).attr('data-level-index')) return;
            if (i || elems.length == 1) el.css('visibility', 'hidden').empty();
        })
        _this.addOpt($(cur_sel).next(), level).setValue(sels);
    },
    setValue: function(sels) {
        var k = [],
            str, id,
            input = this.elem.find('input')[0];
        $.each(sels, function() {
            var opt = $(this).find('[selected]'),
                t = opt.text(),
                v = opt.val();
            if (opt.length && v != '_NULL_') {
                k.push(t);
                id = v;
            }
        });
        if (k.length) {
            str = $(sels).prev('*[package]').attr('package') + ":" + k.join('/');
            input.value = str + ':' + id;
        } else {
            input.value = "";
        }
    },
    isAddSel: function(select) {
        var sels = [],
            select = $(select);
        while (select.attr('data-level-index') <= select.next().attr('data-level-index')) {
            sels.push(select = select.next());
        }
        $.each(sels, function() {
            $(this).empty().css('visibility', 'hidden');
        });
        select.next() && select.next().empty();
    },
    set: function(target, opt) {
        if (opt) {
            opt.selected = true;
        } else {
            opt = target.options[target.selectedIndex];
        }
        $(opt).attr('selected', true).siblings().removeAttr('selected');
        var index = opt.getAttribute('data-level-index');
        this.index = +($(target).attr('data-level-index')) + 1;
        var data = this.data[this.index];
        return data ? data[index] : false;
    },
    init: function(region_panel_id,region_Data,func_callback) {
        this.callback = window[func_callback];
        this.elem = $(region_panel_id);
        var sels = this.elem.find('select');
        this.data = region_Data;
        this.addOpt(sels[0], this.data[0]).isAddSel($(sels[0]).css('visibility', 'visible'));
        this.bindEvent();
        this.tryselected();
    },
    tryselected:function(region_panel_id,val){
        var panel = region_panel_id?$(region_panel_id):this.elem;
        if(val){
            panel.find('input[package]:hidden').val(val);
        }else{
            val = panel.find('input[package]:hidden').val();
        }
        val && $.trim(val)!='' && $.each(val.split(':')[1].split('/'),function(i,s){
                var sel = panel.find('select[data-level-index="'+i+'"]');
                var opt = $(sel).children().filter(function(){
                    return $(this).text()==s;
                });
                if(sel) region_sel.changeResponse(sel,opt[0]);
            });
    },
    unselected:function(region_panel_id){
        var panel = $(region_panel_id),sels = panel.find('select');
        panel.find('input[package]:hidden').val('');
        sels.prop('selectedIndex',0);
        sels.css('visibility','hidden');
        $(sels[0]).css('visibility','visible');
    }
};
