define(['jquery', 'bootstrap', 'backend', 'table', 'form', '../../custom'], function ($, undefined, Backend, Table, Form, Custom) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'site/tag/index' + location.search,
                    add_url: 'site/tag/add',
                    edit_url: 'site/tag/edit',
                    del_url: 'site/tag/del',
                    multi_url: 'site/tag/multi',
                    table: 'multi_tags',
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
                        {field: 'content.title', title: __('Title'),align:'left'},
                        {field: 'type', title: __('Type'), searchList: {"productPage":__('Productpage'),"article":__('Article')}, formatter: Table.api.formatter.normal},
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
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));

                Custom.autoWriteSlug();
            }
        }
    };
    return Controller;
});