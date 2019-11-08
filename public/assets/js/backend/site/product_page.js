define(['jquery', 'bootstrap', 'backend', 'table', 'form', '../../custom', 'adminLte'], function ($, undefined, Backend, Table, Form, Custom, adminLte) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'site/product_page/index' + location.search,
                    add_url: 'site/product_page/add',
                    edit_url: 'site/product_page/edit',
                    del_url: 'site/product_page/del',
                    multi_url: 'site/product_page/multi',
                    table: 'product_pages',
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
                        {field: 'product.model', title: __('Product.model')},
                        {field: 'category.name', title: __('Category.name')},
                        {field: 'languages', title: __('Language'), formatter: function (value, row) {
                                var html = [];
                                $.each(value, function (i, v) {
                                    html.push('<a href=\"/' + v + row.url + '\"><span class="label label-primary">' + v + '</span></a>');
                                });
                                return html.join(' ');
                            }},
                        {field: 'tag_ids', title: __('Tag_ids')},
                        {field: 'view', title: __('View')},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'admin.nickname', title: __('Admin.nickname')},
                        {field: 'post_status', title: __('Post_status'), searchList: {"published":__('Published'),"draft":__('Draft')}, formatter: Table.api.formatter.status, custom:{'published': 'success', 'draft': 'grey'}},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons:
                            [
                                Custom.makeTabBtn('site/product_page/edit')
                            ]
                        }
                    ]
                ],
                onLoadSuccess: function () {
                    $('#btn-add').click(function () {
                        Backend.api.addtabs('site/product_page/add',__('Add'));
                    });
                }
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'site/product_page/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'site/product_page/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'site/product_page/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
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

            $("#c-language").change(function () {
                var id = $('#c-id').val();
                var lang = $(this).val();
                var type = 'productPage';
                Custom.switchLanguageImages(type, id, lang);
                Custom.switchLanguageContent(type, id, lang);
                Toastr.success(__('Operation completed'));
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"), function () {
                    Custom.closeCurrentTab('site/product_page');
                });

                $('#c-product_id').data('params', function () {
                    return {custom:{category_id:$('#c-category_id').val()}};
                }).change(function () {
                    $('#c-title').val($(this).selectPageText).trigger('focusout');
                });

                $('#c-category_id').change(function () {
                    $('#c-product_id').selectPageClear();
                });

                Custom.autoWriteSlug();

                function saveDraft(type) {
                    Custom.saveDraft('site/product_page/savedraft/type/'+type);
                }

                $('#saveDraft').click(function () {
                    saveDraft('manual');
                });

                $('.btn-trash').click(function () {
                    $(this).closest('.form-group').siblings().find('input[type="text"]').val('');
                });

                //自动保存
                setInterval(function(){
                    if ($('#c-slug').val().length > 0){
                        setTimeout( function () {
                            saveDraft('auto');
                        },120000);
                    }
                },300000);
            }
        }
    };
    return Controller;
});