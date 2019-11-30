define(['jquery', 'bootstrap', 'backend', 'table', 'form', '../../custom'], function ($, undefined, Backend, Table, Form, Custom) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sales/order/index' + location.search,
                    add_url: 'sales/order/add',
                    edit_url: 'sales/order/edit',
                    del_url: 'sales/order/del',
                    multi_url: 'sales/order/multi',
                    table: 'orders',
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
                        {field: 'ref_no', title: __('Ref_no')},
                        {field: 'quotation.ref_no', title: __('Quotation'), formatter: function(value, row) {
                                return value ? "<a class='btn-addtabs' title='"+ value + " "+__('Detail') + "' href='sales/quotation/detail/ids/"+row.quotation_id+"'>"+value+"</a>":'';
                            }},
                        {field: 'client.short_name', title: __('Clients.short_name'), formatter: function (value, row) {
                                return "<a class='btn-addtabs' title='"+ value + " "+__('Detail') + "' href='sales/client/detail/ids/"+row.client_id+"'>"+value+"</a>";
                            }},
                        {field: 'country.country_name', title: __('Country')},
                        {field: 'contact.appellation', title: __('Contacts.appellation')},
                        {field: 'currency', title: __('Currency'), searchList: {"USD":__('USD'),"CNY":__('CNY')}, formatter: Table.api.formatter.normal},
                        {field: 'incoterms', title: __('Incoterms'), searchList: {"EXW":__('EXW'),"FCA":__('FCA'),"FAS":__('FAS'),"FOB":__('FOB'),"CFR":__('CFR'),"CIF":__('CIF'),"CPT":__('CPT'),"CIP":__('CIP'),"DAT":__('DAT'),"DAP":__('DAP'),"DDP":__('DDP')}, formatter: Table.api.formatter.normal},
                        {field: 'leadtime', title: __('Leadtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'admin.nickname', title: __('Admin.nickname')},
                        {field: 'status', title: __('Status'), searchList: {"10":__('Pending'),"20":__('Processing'),"30":__('Collected'),"40":__('Completed'),"-1":__('Cancel')}, formatter: Table.api.formatter.status, custom: {"10":"gray","20":"info","30":"warning","40":"success","-1":"danger"}},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons:
                        [
                            Custom.makeTabBtn('sales/order/detail','Detail','fa-list'),
                        ]}
                    ]
                ]
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
                url: 'sales/order/recyclebin' + location.search,
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
                                    url: 'sales/order/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'sales/order/destroy',
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


            $('#c-default_info').change(function () {
                if ($(this).is(':checked')){
                    $('#c-contact_id, #c-country_code').selectPageClear().closest('.form-group').addClass('hidden');
                    $('#c-destination').val('').closest('.form-group').addClass('hidden');
                } else {
                    $('#c-contact_id, #c-country_code, #c-destination').closest('.form-group').removeClass('hidden');
                }
            });
        },
        edit: function () {
            Controller.api.bindevent();
        },
        placeorder: function () {
            Controller.api.bindevent();
        },
        print: function () {
            var beforePrint = function() {
            };
            var afterPrint = function() {
                parent.Layer.closeAll();
            };
            window.onbeforeprint = beforePrint;
            window.onafterprint = afterPrint;
            window.print();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));


                $(".btn-append").on('fa.event.appendfieldlist', function () {
                    Form.events.selectpicker($("form[role=form]"));
                });

                //客户联系人联动
                $('#c-contact_id').data('params', function () {
                    return {custom:{client_id:$('#c-client_id').val()}};
                });
                $("#c-client_id").change(function () {
                    $("#c-contact_id").selectPageClear();
                });


                //根据币种控制汇率税率显示
                $("#c-currency").change(function () {
                    if ($(this).val() === "CNY") {
                        $("#c-switch_tax").closest(".form-group").removeClass('hidden');
                        $("#c-rate").val('1').closest(".form-group").addClass('hidden');
                    } else {
                        $("#c-rate").val('').closest(".form-group").removeClass('hidden');
                        $("#c-switch_tax,#c-vat_rate").closest(".form-group").addClass('hidden');
                        $("#c-switch_tax").attr("checked",false);
                        $("#c-vat_rate").val("0");
                    }
                });

                //控制显示税率控件
                $("#c-switch_tax").change(function () {
                    if ($(this).is(":checked")){
                        $("#c-vat_rate").closest('.form-group').removeClass('hidden');
                    } else {
                        $("#c-vat_rate").val('').closest('.form-group').addClass('hidden');
                    }
                });


                //根据贸易术语控制运输方式，运费，保险税率显示
                $('#c-incoterms').change(function () {
                    var terms = $(this).val();
                    if ($.inArray(terms, ['FCA','FAS','FOB','CFR','CPT']) > -1) {
                        $("#c-transport").closest('.form-group').removeClass('hidden');
                        $('#c-insurance').val('').closest('.form-group').addClass('hidden');
                    } else if ($.inArray(terms, ['CIF','CIP','DAT','DPT','DAP','DDP']) > -1) {
                        $("#c-transport,#c-insurance").closest('.form-group').removeClass('hidden');
                    } else  {
                        $("#c-transport,#c-insurance").val('').closest('.form-group').addClass('hidden');
                    }
                });

                $(function () {
                    if ($('#c-destination').val()) {
                        $('#c-switch').trigger('click');
                    }

                    $('#c-incoterms').trigger('change');

                    if ($("#c-currency").val() === "CNY") {
                        $("#c-switch_tax").closest(".form-group").removeClass('hidden');
                        $("#c-rate").closest(".form-group").addClass('hidden');
                    }

                    if ($("#c-vat_rate").val() > 0) {
                        $("#c-switch_tax").trigger("click");
                    }
                });
            }
        },
        detail: function () {
            $("#btn-edit").click(function () {
                Fast.api.open("sales/order/edit/ids/" + Config.order.id, __('Edit') +' '+ Config.order.ref_no, {callback: function (data) {
                    }
                });
            });

            $("#btn-receivables").click(function () {
                Fast.api.open("accounting/receivables/add/order_id/" +  Config.order.id, __("Receivables"), function () {
                    parent.location.reload();
                });
            });

            $("#btn-print-ci").click(function () {
                Fast.api.open("sales/order/print/type/ci/ids/" + Config.order.id,'',{area:["90%","90%"]});
            });

            $("#btn-print-pl").click(function () {
                Fast.api.open("sales/order/print/type/pl/ids/" + Config.order.id,'',{area:["90%","90%"]});
            });

            $("#btn-follow").click(function () {
                Fast.api.open("sales/follow/add/client_id/"+Config.order.client_id+"/contact_id/"+Config.order.contact_id+"/order_id/"+Config.order.id, __("Add Follow"));
            });


            $("#btn-calendar").click(function () {
                Fast.api.addtabs("calendar/index" ,__('Calendar'));
            });

            Table.api.init({
                showFooter:true,
                extend: {
                    index_url: 'sales/order_item/index' + location.search,
                    add_url: 'sales/order_item/add/currency/' + Config.order.currency + '/order_id/' + Config.order.id,
                    edit_url: 'sales/order_item/edit/currency/'+ Config.order.currency,
                    del_url: 'sales/order_item/del',
                    multi_url: 'sales/order_item/multi',
                    table: 'order_item',
                }
            });

            var table = $("#table");

            var cny = Config.order.currency === "CNY";
            var usd = !cny;
            var tax = Config.order.tax_rate > 0;
            var view = Config.order.status < 20;

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                showColumns: false,
                showToggle: false,
                showExport: false,
                queryParams: function(params){
                    var filter = JSON.parse(params.filter);
                    var op = JSON.parse(params.op);
                    filter.order_id = Config.order.id;
                    op.order_id = '=';
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    return params;
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'variety.code', title: __('Variety'),footerFormatter: function () {
                                return "Total";
                            }},
                        {field: 'accessory', title: __('Accessory'), formatter: function (value) {
                                if (value.length > 0){
                                    return $.map(value, function(val){
                                        return val.name;
                                    }).join(' ');
                                } else {
                                    return '-';
                                }
                            }},
                        {field: 'package.name', title: __('Package')},
                        {field: 'process', title: __('Process'), formatter: function (value, row) {
                                if (value.length > 0) {
                                    return $.map(value, function(val,key){
                                        return val.process;
                                    }).join(' ');
                                } else {
                                    return '-';
                                }
                            }},
                        {field: 'ctn', title: __('Carton'), footerFormatter: function () {
                                return Config.order.total_ctn;
                            }},
                        {field: 'grossw', title: __('Weight'), operate:'BETWEEN', footerFormatter: function () {
                                return Config.order.total_gross_weight;
                            }},
                        {field: 'cbm', title: __('Cbm'), operate:'BETWEEN', footerFormatter: function () {
                                return Config.order.total_cbm;
                            }},
                        {field: 'quantity', title: __('Quantity'), footerFormatter: function (row) {
                                var sum = 0;
                                $.map(row, function (val) {
                                    sum += val.quantity;
                                });
                                return sum;
                            }},
                        //{field: 'profit', title: __('Profit')},
                        {field: 'unit_price', title: __('Unit_price'), operate:'BETWEEN', formatter: function (value) {
                                return "￥ " + value;
                            },visible: cny},
                        {field: 'usd_unit_price', title: __('Unit_price'), operate: "BETWEEN", formatter: function (value) {
                                return "$ " + value;
                            },visible: usd},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN', formatter: function (value) {
                                return "￥ " + value;
                            }, footerFormatter: function (row) {
                                return "￥ " + Config.order.total_amount;
                            },visible: !tax&&!usd},
                        {field: 'usd_amount', title: __('Amount'), operate: 'EETWEEN', formatter: function (value) {
                                return "$ " + value;
                            }, footerFormatter: function (row) {
                                return "$ " + Config.order.total_usd_amount;
                            },visible: usd},
                        {field: 'tax_amount', title: __('Tax Included'), formatter: function (value) {
                                return "￥ " + value;
                            }, footerFormatter: function () {
                                return "￥ " + Config.order.total_tax_amount;
                            },visible: tax},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, visible: view}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        }
    };
    return Controller;
});