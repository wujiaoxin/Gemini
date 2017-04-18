var TableManaged = function () {

    return {

        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            var  configData ={
                "aLengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "全部"] // change per page values here
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
            // 客户管理table  id="table-customer"
            $('#table-customer').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
    				{ "bSortable": false }
                ],
                configData
            });

            jQuery('#table-customer_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
            jQuery('#table-customer_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown
            // jQuery('#table-customer_wrapper .dataTables_length select').select2(); // initialzie select2 dropdown


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
                configData
            });

            jQuery('#table-assetChannel_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
            jQuery('#table-assetChannel_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown
            // jQuery('#table-assetChannel_wrapper .dataTables_length select').select2(); // initialzie select2 dropdown


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
                configData
            });

            jQuery('#table-myStaff_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
            jQuery('#table-myStaff_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown
            // jQuery('#table-myStaff_wrapper .dataTables_length select').select2(); // initialzie select2 dropdown

        }
    };
}();