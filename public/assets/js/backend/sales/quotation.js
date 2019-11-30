define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'adminlte', '../../custom'], function ($, undefined, Backend, Table, Form, Adminlte, Custom) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sales/quotation/index' + location.search,
                    add_url: 'sales/quotation/add',
                    edit_url: 'sales/quotation/edit',
                    del_url: 'sales/quotation/del',
                    multi_url: 'sales/quotation/multi',
                    table: 'quotations',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                showColumns: false,
                showToggle: false,
                showExport: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'ref_no', title: __('Ref_no')},
                        {field: 'client.short_name', title: __('Clients.short_name')},
                        {field: 'po_no', title: __('Po_no')},
                        {field: 'country.country_name', title: __('Country')},
                        {field: 'contact.appellation', title: __('Contacts.appellation')},
                        {field: 'currency', title: __('Currency'), searchList: {"USD":__('USD'),"CNY":__('CNY')}, formatter: Table.api.formatter.normal},
                        {field: 'incoterms', title: __('Incoterms'), searchList: {"EXW":__('EXW'),"FCA":__('FCA'),"FAS":__('FAS'),"FOB":__('FOB'),"CFR":__('CFR'),"CIF":__('CIF'),"CPT":__('CPT'),"CIP":__('CIP'),"DAT":__('DAT'),"DAP":__('DAP'),"DDP":__('DDP')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"10":__('New'),"20":__('Quoted'),"30":__('Print PI'),"40":__('Ordered'),"-1":__('Expired')}, formatter: Table.api.formatter.status, custom: {'10':'info','20':'info','30':'success','-1':'danger'}},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'admin.nickname', title: __('Admin.nickname')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons:
                            [
                                Custom.makeTabBtn('sales/quotation/detail','Detail','fa-list'),
                                /*{
                                    name: 'copy',
                                    title: __('Copy'),
                                    classname: 'btn btn-xs btn-success btn-dialog btn-copyone',
                                    icon: 'fa fa-copy',
                                    extend: 'data-toggle="tooltip" data-area=\'["90%","90%"]\'',
                                    url: 'sales/quotation/copy/update/false',
                                    confirm: '是否复制该报价？',
                                    callback: function (value) {
                                        Fast.api.addtabs("sales/quotation/detail/ids/"+ value.data.ids, value.data.ref_no + ' ' + __("Detail"));
                                    }
                                },*/
                            ]
                        }
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
                url: 'sales/quotation/recyclebin' + location.search,
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
                                    url: 'sales/quotation/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'sales/quotation/destroy',
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
        /*copy: function () {
            Controller.api.bindevent();
            var ids = window.location.pathname.split('/');
            ids = ids[ids.length - 1];
            $(function () {
                Layer.confirm("是否更新到最新参数价格？", {btn:["更新", "维持"]}, function (index) {
                    $("form").attr("action", "sales/quotation/copy/update/true/ids/" + ids + location.search);
                    $("[name*='unit_price'],[name*='usd_unit_price']").val('');
                    Layer.close(index);
                });
            });

            $(".btn-remove").click(function () {
                this.closest(".attachment-block").remove();
            });
        },*/
        print: function () {
            var type = window.location.pathname.split('/');
                type = type[type.length - 3];
            var ids = window.location.pathname.split("/");
                ids = ids[ids.length - 1];
            var status = type === 'pi' ? "30" : "20";
            var beforePrint = function() {
            };
            var afterPrint = function() {
                Fast.api.ajax("sales/quotation/updatestatus/status/" + status + "/ids/" + ids, function () {
                    return false;
                });
                parent.Layer.closeAll();
                parent.location.reload();
            };
            window.onbeforeprint = beforePrint;
            window.onafterprint = afterPrint;
            window.print();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"), function (data) {
                    Backend.api.addtabs('sales/quotation/detail/ids/'+data, __('Detail'));
                });

                $(".btn-append").on('fa.event.appendfieldlist', function () {
                    Form.events.selectpicker($("form[role=form]"));
                });

                //控制客户和联系人
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
                        $("#c-transport,#c-transport_fee").closest('.form-group').removeClass('hidden');
                        $('#c-insurance').val('').closest('.form-group').addClass('hidden');
                    } else if ($.inArray(terms, ['CIF','CIP','DAT','DPT','DAP','DDP']) > -1) {
                        $("#c-transport,#c-transport_fee,#c-insurance").closest('.form-group').removeClass('hidden');
                    } else  {
                        $("#c-transport,#c-transport_fee,#c-insurance").val('').closest('.form-group').addClass('hidden');
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
            $(".btn-edit").click(function () {
                Fast.api.open("sales/quotation/edit/ids/" + Config.quotation.id, __('Edit') +' '+ Config.quotation.ref_no, {callback: function (data) {
                        Layer.confirm("是否自动更新该报价下item的价格",{},function (index) {
                            Fast.api.ajax("sales/quotation/updateitems/ids/"+Config.quotation.id, function () {
                                Layer.close(index);
                                window.location.reload();
                            });
                        });
                    }
                });
            });

            $(".btn-print").click(function () {
                Fast.api.open("sales/quotation/print/type/quotation/ids/" + Config.quotation.id,'',{area:["90%","90%"]});
            });

            $(".btn-print-pi").click(function () {
                Fast.api.open("sales/quotation/print/type/pi/ids/" + Config.quotation.id,'',{area:["90%","90%"]});
            });

            /*$(".btn-copy").click(function () {
                Fast.api.open("sales/quotation/copy/update/false/ids/" + Config.quotation.id, __("Copy"),{
                    callback: function (data) {
                        Fast.api.addtabs("sales/quotation/detail/ids/"+ data.data.ids, data.data.ref_no + ' ' + __("Detail"));
                    },
                    area: ["90%","90%"],
                });
            });*/

            /*$(".btn-order").click(function () {
                Layer.confirm("确定要创建订单吗？", {}, function (index) {
                    Fast.api.open("sales/order/placeorder/ids/" + Config.quotation.id, "创建订单", {
                        callback: function (data) {
                            Fast.api.addtabs("sales/order/detail/ids/"+ data.ids, data.ref_no + " " + __("Detail"));
                            window.location.reload();
                        }
                    });
                    Layer.close(index);
                });
            });*/

            $(".btn-order").click(function () {
                layer.confirm("确定要创建订单吗？", {}, function () {
                    Fast.api.ajax("sales/order/placeorder/ids/" + Config.quotation.id, function (data) {
                        Fast.api.addtabs("sales/order/detail/ids/"+ data.ids, data.ref_no + " " + __("Detail"));
                        window.location.reload();
                    });
                });
            });

            $("#btn-follow").click(function () {
                Fast.api.open("sales/follow/add/client_id/"+Config.quotation.client_id+"/contact_id/"+Config.quotation.contact_id+"/quotation_id/"+Config.quotation.id, __("Add Follow"));
            });

            $("#btn-calendar").click(function () {
                Fast.api.addtabs("calendar/index" ,__('Calendar'));
            });

            // 初始化表格参数配置
            Table.api.init({
                showFooter:true,
                extend: {
                    index_url: 'sales/quotation_item/index' + location.search,
                    add_url: 'sales/quotation_item/add/currency/' + Config.quotation.currency + '/quotation_id/' + Config.quotation.id,
                    edit_url: 'sales/quotation_item/edit/currency/'+ Config.quotation.currency +'/update/false',
                    del_url: 'sales/quotation_item/del',
                    multi_url: 'sales/quotation_item/multi',
                    table: 'quotation_item',
                }
            });


            var table = $("#table");

            var cny = Config.quotation.currency === "CNY";
            var usd = !cny;
            var tax = Config.quotation.tax_rate > 0;
            var view = Config.quotation.status !== "40";

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
                    filter.quotation_id = Config.quotation.id;
                    op.quotation_id = '=';
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
                                    });
                                } else {
                                    return '-';
                                }
                            }},
                        {field: 'package.name', title: __('Package')},
                        {field: 'process', title: __('Process'), formatter: function (value) {
                                if (value.length > 0) {
                                    return $.map(value, function(val){
                                        return val.process;
                                    });
                                } else {
                                    return '-';
                                }
                            }},
                        {field: 'ctn', title: __('Carton'), footerFormatter: function () {
                                return Config.quotation.total_ctn;
                            }},
                        {field: 'grossw', title: __('Weight'), operate:'BETWEEN', footerFormatter: function () {
                                return Config.quotation.total_gross_weight;
                            }},
                        {field: 'cbm', title: __('Cbm'), operate:'BETWEEN', footerFormatter: function () {
                                return Config.quotation.total_cbm;
                            }},
                        {field: 'quantity', title: __('Quantity'), footerFormatter: function (row) {
                                var sum = 0;
                                $.map(row, function (val) {
                                    sum += val.quantity;
                                });
                                return sum;
                            }},
                        {field: 'profit', title: __('Profit')},
                        {field: 'unit_price', title: __('Unit_price'), operate:'BETWEEN', formatter: function (value) {
                                return "￥ " + value;
                            },visible: cny},
                        {field: 'usd_unit_price', title: __('Unit_price'), operate: "BETWEEN", formatter: function (value) {
                                return "$ " + value;
                            },visible: usd},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN', formatter: function (value) {
                                return "￥ " + value;
                            }, footerFormatter: function () {
                                return "￥ " + Config.quotation.total_amount;
                            },visible: !tax&&!usd},
                        {field: 'usd_amount', title: __('Amount'), operate: 'EETWEEN', formatter: function (value) {
                                return "$ " + value;
                            }, footerFormatter: function (row) {
                                return "$ " + Config.quotation.total_usd_amount;
                            },visible: usd},
                        {field: 'tax_amount', title: __('Tax Included'), formatter: function (value) {
                                return "￥ " + value;
                            }, footerFormatter: function () {
                                return "￥ " + Config.quotation.total_tax_amount;
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