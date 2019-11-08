define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template', '../../custom'], function ($, undefined, Backend, Table, Form, Template, Custom) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sales/client/index' + location.search,
                    add_url: 'sales/client/add',
                    edit_url: 'sales/client/edit',
                    del_url: 'sales/client/del',
                    multi_url: 'sales/client/multi',
                    table: 'clients',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'updatetime',
                showColumns: false,
                showToggle: false,
                showExport: false,
                rowStyle: rowStyle,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'logo', title: __('Logo')},
                        {field: 'short_name', title: __('Short_name')},
                        {field: 'category.name', title: __('Source')},
                        {field: 'type', title: __('Type'), searchList: {"零售商":__('零售商'),"批发商":__('批发商'),"品牌商":__('品牌商'),"进口商":__('进口商'),"连锁商场":__('连锁商场'),"企业":__('企业')}, formatter: Table.api.formatter.normal},
                        {field: 'star', title: __('Star'), searchList: {"1":__('Star 1'),"2":__('Star 2'),"3":__('Star 3'),"4":__('Star 4'),"5":__('Star 5')}, formatter: Table.api.formatter.normal},
                        {field: 'country.country_name', title: __('Country'),formatter: function (value, row) {
                                var now = new Date(new Date().getTime()+(row['country']['timezone']-8)*60*60*1000);
                                return "<span data-toggle='tooltip' title='Current Time: "+now.toLocaleString()+"'>"+value+"</span>";
                            }},
                        {field: 'website', title: __('Website')},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'admin.nickname', title: __('Admin')},
                        {field: 'status', title: __('Status'), searchList: {"10":__('Status 10'),"20":__('Status 20'),"30":__('Status 30'),"40":__('Status 40'),"-1":__('Status -1')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons:[
                                Custom.makeTabBtn('sales/client/detail','Detail', 'fa-list'),
                                Custom.makeTabBtn('sales/client/edit')
                            ]
                        }
                    ]
                ],
                onLoadSuccess: function () {
                    $('#btn-add').click(function () {
                        Backend.api.addtabs('sales/client/add',__('Add Client'));
                    });
                }
            });

            function rowStyle(row) {
                if (row['country']['timezone']){
                    var now = new Date(new Date().getTime()+(row['country']['timezone']-8)*60*60*1000).getHours();
                    if (now > 8 && now <= 17) {
                        return {classes:'success'};
                    } else if (now > 17 && now <= 22) {
                        return {classes:'warning'};
                    } else {
                        return {classes:'danger'};
                    }
                } else {
                    return {classes:''};
                }
            }

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
                url: 'sales/client/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
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
                                    url: 'sales/client/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'sales/client/destroy',
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
            $('#c-contact_id').data('params', function () {
                return {custom: {client_id:$('#c-id').val()}};
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"), function () {
                    Custom.closeCurrentTab('sales/client');
                },'',function () {
                    var box_body = $(".box-body:eq(1)");
                    var size = box_body.children().size();
                    if (size <= 1) {
                        box_body.closest('.box').addClass("box-danger box-solid");
                        box_body.children("span").addClass("text-danger").text("联系人不能为空");
                        return false;
                    } else {
                        box_body.children("span").addClass("text-success").text("");
                        box_body.closest('.box').removeClass("box-default box-danger box-solid").addClass("box-success box-solid");
                        //Form.api.submit(this);
                        return true;
                    }
                });

                //添加联系人表格
                $('#addForm').click(function () {
                    Controller.api.buildContactForm({});
                    var box_body = $(".box-body:eq(1)");
                    box_body.closest('.box').removeClass('box-danger');
                    box_body.children("span").removeClass('text-danger').text('');
                    $('.delForm').click(function () {
                        $(this).closest('.contactForm').remove();
                    });
                });

                //国家和城市联动
                $('#c-city_code').data('params', function () {
                    return {custom:{country_code:$('#c-country_code').val()}};
                });
                $('#c-country_code').change(function () {
                    $('#c-city_code').selectPageClear();
                });

                //删除联系人表格按钮
                $('.delForm').click(function () {
                    $(this).closest('.contactForm').remove();
                });
            },
            buildContactForm: function (data) {
                var i = $('.contactForm').length;
                var html = Template('contactFormTemplate', {index:i,data:data});
                $('#addForm').closest('.box-header').nextAll('.box-body').append(html);
            }
        }
    };
    return Controller;
});