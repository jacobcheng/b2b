define(['jquery', 'bootstrap', 'form'], function ($, undefined, Form) {
    var Controller = {
        makeTabBtn: function (url, name, icon) {
            name = name || 'Edit';
            icon = icon || 'fa-pencil';
            var btn = {
                name: name.toLocaleLowerCase(),
                title: __(name),
                icon: 'fa '+ icon,
                classname: 'btn btn-xs btn-success btn-addtabs',
                url: url,
                extend: 'data-toggle="tooltip"'
            };
            return btn;
        },
        closeCurrentTab: function (url) {
            var obj = top.window.$("ul.nav-addtabs li.active");
            top.window.$(".sidebar-menu a[url$='"+url+"'][addtabs]").click();
            top.window.$(".sidebar-menu a[url$='"+url+"'][addtabs]").dblclick();
            obj.find(".fa-remove").trigger("click");
        },
        autoWriteSlug: function () {
            if (!$('#c-slug').val()) {
                $('#c-title').focusout(function () {
                    var title = $(this).val();
                    if (title){
                        var slug = title.trim().toLocaleLowerCase().replace(new RegExp(/ /g), '-');
                        $('#c-slug').val(slug);
                    }
                });
            }
        },
        saveDraft: function (url) {
            if (!$('#c-slug').val()){
                return;
            }
            Form.api.submit($("form[role=form]"),function (data) {
                var form = $("form[role=form]");
                form.attr('action','');
                if (!$('#c-id').length){
                    var path = window.location.pathname.replace(/add/,'edit');
                    var html = '<input id="c-id" class="hidden" name="row[id]" value="'+data.id+'" >';
                    form.prepend(html);
                    form.attr('action',path+'/ids/'+data.id);
                }
            },function () {

            },function () {
                $("form[role=form]").attr('action',url);
            });
        },
        switchLanguageContent: function(type, id, lang) {
            Backend.api.ajax({url:'/api/content/getcontent', data:{type:type,id:id,lang:lang}}, function (data) {
                if (!data){
                    $('#c-seo_title, #c-keyword, #c-description, #c-title').val('').removeAttr('readonly');
                    KindEditor.instances[0].text('');
                } else {
                    $('#c-seo_title').val(data.seo_title);
                    $('#c-keyword').val(data.keyword);
                    $('#c-description').val(data.description);
                    KindEditor.instances[0].text(data.content);
                }
                return false;
            });
        },
        switchLanguageImages: function (type, id, lang) {
            Backend.api.ajax({url:'/api/image/getimage',data:{type:type,id:id,lang:lang}}, function (data) {
                $('input[name^="row[images]"]').not('input[name="row[images][language]"]').val('').trigger('change');
                if (data.length > 0) {
                    $.each(data, function (index, row) {
                        $('#c-images-'+index+'-url').val(row.url).trigger('change');
                        $('#c-images-'+index+'-title').val(row.title);
                        $('#c-images-'+index+'-alt').val(row.alt);
                    });
                }
                return false;
            });
        }
    };
    return Controller;
});