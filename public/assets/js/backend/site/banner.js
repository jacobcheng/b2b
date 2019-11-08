define(['jquery', 'bootstrap', 'backend', 'table', 'form', '../../custom', 'adminLte', 'template'], function ($, undefined, Backend, Table, Form, Custom, adminLte, Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'site/banner/index' + location.search,
                    add_url: 'site/banner/add',
                    edit_url: 'site/banner/edit',
                    del_url: 'site/banner/del',
                    multi_url: 'site/banner/multi',
                    table: 'banner',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'name', title: __('Name')},
                        {field: 'languages', title: __('Language'), formatter: function (value) {
                                var html = [];
                                $.each(value, function (i,v) {
                                    html.push('<span class="label label-primary">'+v+'</span>');
                                });
                                return html.join(' ');
                            }},
                        {field: 'width', title: __('Width'), searchList: {"fullwidth":__('Fullwidth'),"fullscreen":__('Fullscreen')}, formatter: Table.api.formatter.normal},
                        {field: 'height', title: __('Height')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons:
                                [
                                    Custom.makeTabBtn('site/banner/edit')
                                ]
                        }
                    ]
                ],
                onLoadSuccess: function () {
                    $('#btn-add').click(function () {
                        Backend.api.addtabs('site/banner/add', __('Add'));
                    });
                }
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();

            $('#c-language').change(function () {
                var lang = $(this).val();
                var id = $('#c-id').val();
                Backend.api.ajax({url:'/api/image/getimage',data:{type:'banner',id:id,lang:lang}}, function (data) {
                    $('input[name$="[url]"]').closest('.box').remove();
                    if(data.length === 0){
                        Controller.api.buildimage({});
                    } else {
                        $.each(data,function (index, row) {
                            Controller.api.buildimage(row);
                        });
                    }
                });
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"), function () {
                    Custom.closeCurrentTab('site/banner');
                });

                $('#addImage').click(function () {
                    Controller.api.buildimage({});
                });

                $('.btn-trash').click(function () {
                    $(this).closest('.form-group').siblings().find('input[type="text"]').val('');
                });
            },
            buildimage: function (data) {
                var i = $('input[name$="[url]"]').closest('.box').length;
                var html = Template('image_template', {index:i,num:i+1,data:data});
                $('.box:eq(1)').after(html);
                Form.api.bindevent($("form[role=form]"), function () {
                    Custom.closeCurrentTab('site/banner');
                });
            }
        }
    };
    return Controller;
});