var TableManaged = function () {

    return {

        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }

            var  config = {
                // change per page values here
                "aLengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "全部"]
                ],
                // set the initial value
                "iDisplayLength": 5,
                "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "每页显示 _MENU_ 条记录",
                    "sZeroRecords": "抱歉， 没有找到",
                    "sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
                    "sInfoEmpty": "没有数据",
                    "sInfoFiltered": "(从 _MAX_ 条数据中检索)",
                    "oPaginate": {
                        "sFirst": "首页",
                        "sPrevious": "前一页",
                        "sNext": "后一页",
                        "sLast": "尾页"
                    },
                    "sZeroRecords": "没有检索到数据"
                },
                "aoColumnDefs": [{
                        'bSortable': true,
                        'aTargets': [0]
                    }
                ]
            };
            // 我的员工页面-我的员工table  id="table-myStaff"
            $('#table-myStaff').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
    				{ "bSortable": false }
                ],
                "aLengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "全部"]
                ],
                config
            });
            jQuery('#table-myStaff_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
            jQuery('#table-myStaff_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown
            // jQuery('#table-myStaff_wrapper .dataTables_length select').select2(); // initialzie select2 dropdown

            // 资产渠道table  id="table-assetChannel"
            $('#table-assetChannel').dataTable({
                "aoColumns": [
                    { "bSortable": false },
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    { "bSortable": false }
                ],
                config
            });
            jQuery('#table-assetChannel_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
            jQuery('#table-assetChannel_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown
            // jQuery('#table-assetChannel_wrapper .dataTables_length select').select2(); // initialzie select2 dropdown


            // 借款项目-借款项目table  id="table-loanItem"
            $('#table-loanItem').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ],
                config
            });
            jQuery('#table-loanItem_wrapper .dataTables_filter input').addClass("m-wrap medium");
            jQuery('#table-loanItem_wrapper .dataTables_length select').addClass("m-wrap small");



            // 还款项目-还款项目table  id="table-repayItem"
            $('#table-repayItem').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ],
                config
            });
            jQuery('#table-repayItem_wrapper .dataTables_filter input').addClass("m-wrap medium");
            jQuery('#table-repayItem_wrapper .dataTables_length select').addClass("m-wrap small");


            // 支付项目-支付项目table  id="table-payItem"
            $('#table-payItem').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ],
                config
            });
            jQuery('#table-payItem_wrapper .dataTables_filter input').addClass("m-wrap medium");
            jQuery('#table-payItem_wrapper .dataTables_length select').addClass("m-wrap small");


            $('#table-withdraw').dataTable({
                "aoColumns": [
                    { "bSortable": false },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ],
               config
            });
            jQuery('#table-withdraw_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
            jQuery('#table-withdraw_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown

             // 审核管理模块-订单列表table  id="table-orderExamine"
            $('#table-orderExamine').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    { "bSortable": false }
                ],
                config
            });

            jQuery('#table-orderExamine_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
            jQuery('#table-orderExamine_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown
            // jQuery('#table-orderExamine_wrapper .dataTables_length select').select2(); // initialzie select2 dropdown


        }
    };
}();