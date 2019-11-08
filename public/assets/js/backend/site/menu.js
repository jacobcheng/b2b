define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'site/menu/index' + location.search,
                    add_url: 'site/menu/add',
                    edit_url: 'site/menu/edit',
                    del_url: 'site/menu/del',
                    multi_url: 'site/menu/multi',
                    dragsort_url: 'ajax/weigh',
                    table: 'menus',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                escape: false,
                queryParams: function(params){
                    params.type = 'main';
                    return params;
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'type', title: __('Type')},
                        {field: 'name', title:__('Title'), align: 'left', formatter:function (value, row) {
                                var suffix = '';
                                if (row.url !== '/') {
                                    suffix = '.html';
                                }
                                return "<a href='/"+row.url+suffix+"' target='_blank'>"+value+"</a>";
                            }},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            //绑定TAB事件
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                // var options = table.bootstrapTable(tableOptions);
                var typeStr = $(this).attr("href").replace('#t-', '');
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    // params.filter = JSON.stringify({type: typeStr});
                    params.type = typeStr;

                    return params;
                };
                //table.bootstrapTable('refresh', {});
                return false;

            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            Form.api.bindevent($("form[role=form]"),function () {
                $(".btn-refresh").trigger("click");
                $('#c-menu_id').selectPageClear();
                $('#c-name, #c-weigh').val();
            });

            $('#c-pid').data('params', function () {
                return {custom:{type:$('#c-type').val()}, isTree:'true'};
            });

            $('#c-menu_id').data('params', function () {
                return {custom:{type:$('#c-menu_type').val()}, isTree:'true'};
            }).change(function () {
                $('#c-name').val($.trim($(this).selectPageText()));
            });
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

                $('#c-pid').data('params', function () {
                    return {custom:{type:$('#c-type').val()}, isTree:'true'};
                });
            }
        }
    };
    return Controller;
});