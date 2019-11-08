define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'site/widget/index' + location.search,
                    add_url: 'site/widget/add',
                    edit_url: 'site/widget/edit',
                    del_url: 'site/widget/del',
                    multi_url: 'site/widget/multi',
                    table: 'widgets',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'content.title', title: __('Title')},
                        {field: 'location', title: __('Location')},
                        {field: 'widget_type', title: __('Widget_type'), searchList: {"current_blog":__('Current_blog'),"current_product":__('Current_product'),"gallery":__('Gallery'),"text":__('Text'),"button":__('Button'),"contact_info":__('Contact_info'),"menu":__('Menu'),"contact_form":__('Contact_form'),"social_links":__('Social_links'),"search_form":__('Search_form'),"image":__('Image'),"navigation":__('Navigation'),"tags":__('Tags')}, formatter: Table.api.formatter.normal},
                        {field: 'languages', title: __('Language'), formatter: function (value) {
                                var html = [];
                                $.each(value, function (i,v) {
                                    html.push('<span class="label label-primary">'+v+'</span>');
                                });
                                return html.join(' ');
                            }},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
            $('#c-widget_type').trigger('change');

            $('#c-language').change(function () {
                var type = 'widget';
                var id = $('#c-id').val();
                var lang = $(this).val();
                Backend.api.ajax({url: '/api/content/getcontent',data:{type:type,id:id,lang:lang}},function (data) {
                    if (data) {
                        $('#c-title').val(data.title);
                        $.each(data.content, function (index, value) {
                            $('#c-'+$('#c-widget_type').val()+'-'+index).val(value);
                        });
                    } else {
                        $('#c-title').val('');
                        $('[name^="row[content][content]"]').val('');
                    }
                });
            });

            $(function () {
                var widget = $('#c-widget_type').val();
                var allContent = $('[name^="row[content][content]"]');
                $('[id^=c-'+widget+']').closest('.form-group').removeClass('hidden');
                allContent.not('[id^=c-'+widget+']').val('');
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));

                $('#c-widget_type').change(function () {
                    var widget = $(this).val();
                    var allContent = $('[name^="row[content][content]"]');
                    $('[id^=c-'+widget+']').closest('.form-group').removeClass('hidden');
                    allContent.not('[id^=c-'+widget+']').val('').closest('.form-group').addClass('hidden');
                });
            }
        }
    };
    return Controller;
});